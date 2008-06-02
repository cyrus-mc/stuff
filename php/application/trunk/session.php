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
	 * @access protected
	 * @var boolean
	 */
	protected $success = true;
	
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
		//if ( count($HTTP_POST_VARS) > 0 )
		//	$this->reponse = $HTTP_POST_VARS;
		//else
		//	$this->response = $HTTP_GET_VARS;
			
		/* set the base URL - take into consideration user directories */		
		//if ($_SERVER['REQUEST_URI'][1] == '~')
		//	$this->base_url = '/' . substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], '/', 1) - 1);
		//else
		//	$this->base_url = "";		
	}
	
	/**
	 * Grab data from the session
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function session_get($key) {
		$object = false;		
		if (isset($_SESSION[$key]))
			$object = $_SESSION[$key];
		else
			$this->set_error("session::get($key) - not found in session.");
		
		return $object;
	}
	
	/**
	 * Set the value of a key within the session
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @return void
	 */
	public function session_register($key, $data, $overwrite = false) {		
		if ($overwrite || ! isset($_SESSION[$key]))
			$_SESSION[$key] = $data;			
		else 
			$this->set_error("session::session_register($key, ...) - overwrite = $overwrite - key already exists in session.");
		
		$this->raise_error();
	}
	
	/**
	 * Remove an object from the session
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function session_unregister($key) {
		if (isset($_SESSION[$key]))
			unset($_SESSION[$key]);	 
		else
			$this->set_error("session::session_unregister($key) - specified key not found in session.");
	
		$this->raise_error();
	}
	
	/**
	 * Retrieve value from query string
	 * 
	 * @param string $key
	 * @param int $flags
	 * @return string
	 */
	public function query_get($key, $flags) {
		$query_value = "";
		if (isset($this->response[$key]))
			$query_value = $this->response[$key];
			
			/* perform safey checks on query string */
			if ( ($flags & self::FILESYSTEM_SAFE) == self::FILESYSTEM_SAFE )
				$this->filesystem_safe($query_value);				
			
			if ( ($flags & self::SQL_SAFE) == self::SQL_SAFE )
				$this->sql_safe($query_value);				
		else
			$this->set_error("session::query_get($key, ..) - specified key not found in query.");

		return $query_value;
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
}
?>
