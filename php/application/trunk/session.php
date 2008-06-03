<?php
/*
	vim:ts=3:sw=3:

	XML configuration parser
   
	$Author: $
	$Date: $
	$Revision: $	
*/

class session {
	
	const FILESYSTEM_SAFE = 1;
	const SQL_SAFE = 2;
	
	/**
	 * @access private
	 * @var array - access to post/get fields
	 */
	private $response;
	
	/**
	 * @access private
	 * @var string - base URL
	 */
	private $base_url;
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	protected $errstr = "";
	
	/**
	 * Default constructor
	 * 
	 * NOTE: ensure all necessary include files are included prior to
	 * instantiating this class
	 */
	public function __construct() {
		/* start the session */
		session_start();
		
		/* set the $response variable */
		if ( count($HTTP_POST_VARS) > 0 )
			$this->reponse = $HTTP_POST_VARS;
		else
			$this->response = $HTTP_GET_VARS;
			
		/* set the base URL - take into consideration user directories */		
		if ($_SERVER['REQUEST_URI'][1] == '~')
			$this->base_url = '/' . substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], '/', 1) - 1);
		else
			$this->base_url = "";		
	}
	
	/**
	 * Grab data from the session
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function session_get($key) {
		if (isset($_SESSION[$key])) {
			$object = $_SESSION[$key];
			return $object;
		}
		
		self::$errstr = "session::get($key) - not found in session.";
		return false;
	}
	
	/**
	 * Set the value of a key within the session
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @return boolean
	 */
	public function session_register($key, $data, $overwrite = false) {		
		if ($overwrite || ! isset($_SESSION[$key])) {
			$_SESSION[$key] = $data;
			return true;			
		}
		
		self::$errstr = "session::session_register($key, ...) - overwrite = $overwrite - key already exists in session.";
		return false;
	}
	
	/**
	 * Remove an object from the session
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function session_unregister($key) {
		if (isset($_SESSION[$key])) {
			unset($_SESSION[$key]);
			return true;
		}
		
		self::$errstr = "session::session_unregister($key) - specified key not found in session.";	
		return false;
	}
	
	/**
	 * Retrieve value from query string
	 * 
	 * @param string $key
	 * @param int $flags
	 * @return mixed
	 */
	public function query_get($key, $flags) {
		if (isset($this->response[$key])) {
			$query_value = $this->response[$key];

			/* perform safety checks on query string */
			if ( ($flags & self::FILESYSTEM_SAFE) == self::FILESYSTEM_SAFE )
				$this->filesystem_safe($query_value);
			
			if ( ($flags & self::SQL_SAFE) == self::SQL_SAFE )
				$this->sql_safe($query_value);

			return $query_value;
		}
		
		self::$errstr = "session::query_get($key, ..) - specified key not found in query.";
		return false;
	}

	/**
	 * Parse form string to make it filesystem safe
	 * 
	 * @param string &$qvalue
	 * @return void
	 */
	private function filesystem_safe(&$qvalue) {		
		/* search and delete any occurences of ../ */		
		$qvalue = preg_replace('/\.\.\//', '', $qvalue);		
	}
	
	/**
	 * Parse form string to make it SQL safe
	 * 
	 * @param string &$qvalue
	 * @return void
	 */
	private function sql_safe(&$qvalue) {
		/* TODO: complete */
	}
}
?>
