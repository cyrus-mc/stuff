<?php
/*
	vim:ts=3:sw=3:

	Implementation of an Application Container, used to provide global objects
	to all requests of an application
   
	TODO: locking mechanism for returned objects (readers and writers)

	$Author: $
	$Date: $
	$Revision: $	
*/

require_once 'sysvipc/rw_fifo.php';

class application_container {
	
	const BASE_DIR = '/tmp/';
	const OBJECT_TYPE_VARIABLE = 'simple_var';
		
	const READ_ACCESS = 0;
	const WRITE_ACCESS = 1;		
	
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
	 * @var boolean - flag used to indicate whether the environment was
	 * 				  already initialized
	 */
	private $already_initialized = false;
	
	/**
	 * @access private
	 * @var array - list of register objects
	 */
	private $objects = array();	
	
	/**
	 * @access private
	 * @var object - read/write semaphore
	 */
	private $reader_writer = null;
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	protected $errstr = "";
	
	/**
	 * @access protected
	 * @var boolean
	 */
	protected $success = true;
			
	/**
	 * Default constructor. 
	 * Initialize application space if not already done
	 * 
	 * @param string $name
	 * @return void
	 */
	public function __construct($name) {
		/* remove any occurences of ../ to prevent access to files outside of BASE_DIR */
		$this->name = preg_replace('/\.\.\//', '', $name);
		$this->application_home = self::BASE_DIR . $this->name;
						
		$is_valid = false;
		/* prevent multiple access */
		$this->reader_writer = new rw_fifo($name);
		$this->reader_writer->write_request();
							
		if (file_exists($this->application_home))
			$is_valid = $this->reinitialize();		
		else
			$is_valid = $this->initialize();
						
		$this->reader_writer->write_release();							
		
		/* check return status and throw exception if found */
		if (! $is_valid)			
			throw new Exception($this->errstr);		
	}	
		
	/**
	 * Initialize the application container	 
	 * 
	 * @return boolean
	 */
	private function initialize() {
		/* create the application home */
		if ( (mkdir($this->application_home) && ($init_file = fopen($this->application_home . '/.init', 'x')) &&
			fwrite($init_file, date("F j, Y, g:i a"))) == false )
				$this->set_error("application_container::initialize() - failed to create application home directory.");

		return $this->raise_error();
	}
	
	/**
	 * Reinitialize an already running application
	 * 
	 * @return boolean
	 */
	private function reinitialize() {
		/* set the already initialized flag */
		$this->already_initialized = true;
		
		/* read in the .init file */
		if ( ($this->init_dt = file_get_contents($this->application_home . "/.init")) ) {
			/* read all files of format key.object_type from application home */
			foreach(glob($this->application_home . "/*.*", GLOB_NOSORT | GLOB_ERR) as $object_file) {
				if (is_file($object_file)) {
					list($key, $object_type) = split('\.', basename($object_file));				
					$this->register_object($key, $object_file);
				} else
					$this->set_error("application_container::reinitialize() - unable to read $object_file.");				
			}
		} else
			$this->set_error("application_container::reinitialize() - failed to reinit application .init file."); 		
							
		return $this->raise_error();
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
		if (isset($this->objects[$key]))
			$this->set_error("application_container::register_object($key) - object with key already registered with application.");
		else
			$this->objects[$key] = array('file' => $filename, 'sem_id' => sem_get(ftok($filename, 'a'), 1, 0666, true));
			
		return $this->raise_error();			
	}
	
	/**
	 * Add an object with the application environment
	 * 
	 * Creates lock file used for concurrency 
	 * 
	 * @param string $key
	 * @param mixed $object
	 * @return boolean
	 */
	public function add_object($key, &$object, $overwrite = false) {
		if ($overwrite)
			$this->remove_object($key);								
					
		/* lock */
		//$this->reader_writer->write_request();
		if (isset($this->objects[$key]))
			$this->set_error("application_container::add_object($key, ..) - object with key already in application environment.");							
		else {		
			/* serialize the object and write it to disk */		
			$object_file = "$this->application_home/$key.";
			if (is_object($object))
				$object_file .= get_class($object);
			else
				$object_file .= self::OBJECT_TYPE_VARIABLE;
					
			if ( ($object_fd = fopen($object_file, "x")) && fwrite($object_fd, serialize($object)) ) {
				$this->objects[$key] = array('file' => $object_file, 'sem_id' => sem_get(ftok($object_file, 'a'), 1, 0666, true));
				fclose($object_fd);
			} else
				$this->set_error("application_container::add_object($key, ..) - failed to write object to application evironment.");							
		}		
		
		/* clear the object, forcing users to get it (ie: lock) */
		$object = null;
		/* unlock */
		//$this->reader_writer->write_release();
		return $this->raise_error();		
	}
	
	/**
	 * Set the object referenced by $key in application environment
	 * 
	 * @param string $key
	 * @param mixed $object
	 * @return boolean
	 */
	public function set_object($key, $object) {		
		if (isset($this->objects[$key])) {
			if ( ($object_fd = fopen($this->objects[$key]['file'], "w")) && fwrite($object_fd, serialize($object)) )
				fclose($object_fd);
			else
				$this->set_error("application_container::set_object($key, ...) - failed to write object to application environment.");

			sem_release($this->objects[$key]['sem_id']);			
		} else
			$this->set_error("application_container::set_object($key, ...) - object does not exist in application environment.");

		return $this->raise_error();
	}
	
	/**
	 * Re-construct the serialized object from the application environment
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get_object($key) {
		$object = null;
		
		if (isset($this->objects[$key]))					
			if (($serialized_object = @file_get_contents($this->objects[$key]['file']))) {
				$object = unserialize($serialized_object);
				/* acquire the semaphore */
				sem_acquire($this->objects[$key]['sem_id']);
			} else {
				$this->set_error("application_container::get_object($key) - failed to open serialized object.");
				$object = $this->raise_error();
			}						

		return $object;
	}
	
	public function has_object($key) { return isset($this->objects[$key]); }
	
	/**
	 * Remove an object from the application environment
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove_object($key) {
		if (isset($this->objects[$key]))
			if (@unlink($this->objects[$key]['file'])) {
				/* remove the semaphore */
				sem_remove($this->objects[$key]['sem_id']);
				unset($this->objects[$key]);
			} else			
				$this->set_error("application_container::remove_object($key) - failed to remove object from application environment.");
		else
			$this->set_error("application_container::remove_object($key) - object does not exist in application environment.");

		return $this->raise_error();
	}
	
	/**
	 * Destroy the application environment
	 * 
	 * @return boolean
	 */
	public function destroy() {	
		$this->reader_writer->write_request();	
		/* clean up all stored objects (if anything fails, the rmdir below will fail) */
		foreach ($this->objects as $key => $objects)
			$this->remove_object($key);		

		if ( (@unlink("$this->application_home/.init") && @rmdir($this->application_home)) == false)			
			$this->set_error("application_container::destroy() - unable to delete application environment home.");
				
		$this->reader_writer->write_release();
		return $this->raise_error();
	}
	
	/**
	 * Lock read access to the application environment
	 * 
	 * @return void
	 */
	public function lock_read() { $this->reader_writer->read_request(); }
	
	/**
	 * Release lock read access to the application environment
	 * 
	 * @return void
	 */
	public function read_release() { $this->reader_writer->read_release(); }
	
	/**
	 * Lock write access to the application environment
	 * 
	 * @return void
	 */
	public function lock_write() { $this->reader_writer->write_request(); }
	
	/**
	 * Release lock write access to the application environment
	 * 
	 * @return void
	 */
	public function write_release() { $this->reader_writer->write_release(); }
	
	/**
	 * Set the error string and raise error flag
	 * 
	 * @param string - error description
	 * @return void;
	 */
	private function set_error($string) {
		$this->errstr = $string;
		$this->success = false;
	}
	
	/**
	 * Return the error flag (true means no error, false indicates failure)
	 * 
	 * @return boolean
	 */
	private function raise_error() {
		/* save the current error flag */
		$current_err_flag = $this->success;
		/* reset error flag to true */
		$this->success = true;
		return $current_err_flag;
	}
	
	/**
	 * Return the current error string
	 * 
	 * @return string	 
	 */
	public function get_errstr() { return $this->errstr; }		

	/**
	 * Return the initialization date and time of the application
	 * container
	 * 
	 * @return string (format: Month Day, Year, HH:mm)
	 */
	public function get_init_dattime() { return $this->init_dt; }			
	
	/**
	 * Returns the already_initialized flag
	 * 
	 * @return boolean
	 */
	public function get_init_flag() { return $this->already_initialized; }
	
	/**
	 * Print the application environment (current objects, total size, etc)
	 * 
	 * TODO: still have to complete this function
	 * 
	 * @return void
	 */
	public function print_app_environment() {
		print "Application :: objects ::\n";
		foreach ($this->objects as $object => $object_details) {
			print "\t- $object\n";
		}
	}
}
?>
