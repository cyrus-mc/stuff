<?php
/*
	vim:ts=3:sw=3:

	Implementation of an Application Container, used to provide global objects
	to all requests of an application
   
	$Author: $
	$Date: $
	$Revision: $

	TODO: entry points: __construct, add_object, get_object, remove_object and destroy
*/

require_once '../../sysvipc/trunk/rw_semaphore.php';

class application_container {
	
	const BASE_DIR = '/tmp/';
	
	const READ_ACCESS = 0;
	const WRITE_ACCESS = 1;
	
	const OBJECT_TYPE_VARIABLE = 'simple_var';
	
	/**
	 * @access private
	 * @var string - application name
	 */
	private $name = null;
	
	/**
	 * @access private
	 * @var string - application environment home
	 */
	private $application_home = null;
	
	/**
	 * @access private
	 * @var string - application initialization date - time
	 */
	private $init_dt = null;
	
	/**
	 * @access private
	 * @var array - list of register objects
	 */
	private $objects = array();	
	
	private $lock_sem_id = null;
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	static protected $errstr = "";
	
	/**
	 * Default constructor. 
	 * Initialize application space if not already done
	 * 
	 * @param string $name
	 * @return void
	 */
	public function __construct($name) {
		/* remove any leading ../ to prevent access to files outside of BASE_DIR */
		$this->name = preg_replace('/[^\.\/]*\.\.\//', '', $name);
		$this->application_home = self::BASE_DIR . $this->name;		
				
		/* prevent multiple access */
		if (!$this->lock_sem_id = sem_get(ftok(__FILE__, 'a'), 1)) {
			self::$errstr = "application_home::__construct - failed to get semaphore ID.";
			return false;
		}
		
		sem_acquire($this->lock_sem_id);
		
		/* must add locking around here */
		if (file_exists($this->application_home))
			$this->reinitialize();		
		else
			$this->initialize();

		sem_release($this->lock_sem_id);		
	}	
	
	/**
	 * Initialize the application container	 
	 * 
	 * @return boolean
	 */
	private function initialize() {				
		/* create the application home */
		if ( !(mkdir($this->application_home) && ($init_file = fopen($this->application_home . '/.init', 'x')) &&
			fwrite($init_file, date("F j, Y, g:i a"))) ) {
			self::$errstr = "application_container::initialize() - failed to create application home directory.";
			return false;
		}
		return true;
	}
	
	/**
	 * Reinitialize an already running application
	 * 
	 * @return boolean
	 */
	private function reinitialize() {		
		/* read in the .init file */
		if ( !($this->init_dt = file_get_contents($this->application_home . "/.init")) ) {
			self::$errstr = "application_container::reinitialize() - failed to reinit application .init file.";
			return false;
		}		
		
		/* read all files of format key.object_type from application home */
		foreach(glob($this->application_home . "/*.*", GLOB_NOSORT | GLOB_ERR) as $object_file) {
			if (is_file($object_file)) {
				list($key, $object_type) = split('\.', basename($object_file));				
				$this->register_object($key, $object_file);
			} else {
				self::$errstr = "application_container::reinitialize() - unable to read $object_file.";
				return false;		
			}
		}			
		return true;
	}
	
	/**
	 * Register an object within the application environment (don't actually construct it)
	 * 
	 * @param string $key
	 * @param string $filename
	 * 
	 * @return boolean
	 */
	private function register_object($key, $filename) {
		if (isset($this->objects[$key])) {
			self::$errstr = "application_container::register_object($key) - object with key already registered with application.";
			return false;
		}
		
		$this->objects[$key] = array('file' => $filename);
		return true;			
	}
	
	/**
	 * Add an object with the application environment
	 * 
	 * @param string $key
	 * @param mixed $object_type
	 * @return void
	 */
	public function add_object($key, $object, $overwrite = false) {
		if ($overwrite)
			$this->remove_object($key);								
		
		if (isset($this->objects[$key])) {
			self::$errstr = "application_container::add_object($key, ..) - object with key already in application environment.";
			return false;		
		}
		
		/* serialize the object and write it to disk */		
		$object_file = "$this->application_home/$key.";
		if (is_object($object))
			$object_file .= get_class($object);
		else
			$object_file .= self::OBJECT_TYPE_VARIABLE;
					
		if ( !(($object_fd = fopen($object_file, "x")) && fwrite($object_fd, serialize($object))) ) {
				self::$errstr = "application_container::add_object($key, ..) - failed to write object to application evironment.";
				return false;
		}		
		
		return $this->register_object($key, $object_file);
	}
	
	/**
	 * Re-construct the serialized object from the application environment
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get_object($key) {
		if ($this->objects[$key]) {
			/* filename format - key.object_type */			
			if (($serialized_object = file_get_contents($this->objects[$key]['file'])))
				return unserialize($serialized_object);
			else
				self::$errstr = "application_container::get_object($key) - failed to open serialized object.";
		}
		return false;
	}
	
	/**
	 * Remove an object from the application environment
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove_object($key) {
		if (isset($this->objects[$key])) {
			if (!@unlink($this->objects[$key]['file'])) {
				self::$errstr = "application_container::remove_object($key) - failed to remove object from application environment.";
				return false;				
			}
			/* unset the associative array */
			unset($this->objects[$key]);
			return true;
		} else {
			self::$errstr = "application_container::remove_object($key) - object does not exist in application environment.";
			return false;
		}
	}
	
	/**
	 * Destroy the application environment
	 * 
	 * @return boolean
	 */
	public function destroy() {		
		/* clean up all stored objects (if anything fails, the rmdir below will fail) */
		foreach ($this->objects as $key => $objects)
			$this->remove_object($key);

		if (!@unlink("$this->application_home/.init")) {			
			self::$errstr = "application_container::destroy() - unable to delete application initialization file.";
			return false;
		}
		
		/* now delete the application environment */
		if (!@rmdir($this->application_home)) {
			self::$errstr = "application_container::destroy() - unable to delete application environment home.";
			return false;
		}
					
		/* remove the semaphore */
		if (!sem_remove($this->lock_sem_id)) {
			self::$errstr = "application_container::destroy() - unable to remove semaphore.";
			return false;
		}
				
		return true;
	}
	
	public function print_contents() { print_r($this->objects); }
	
	public function print_app_environment() {
		print "Application :: objects ::\n";
		foreach ($this->objects as $object => $object_details) {
			print "\t- $object\n";
		}
	}
	
	/**
	 * Print the error string
	 * 
	 * @return void
	 */
	public function print_err() { print self::$errstr . "\n"; }
}
?>