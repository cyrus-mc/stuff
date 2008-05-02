<?php
/*
   vim:ts=3:sw=3:

   Implementation of base level class for database access with caching

   $Author: $
   $Date: $
   $Revision: $
*/

require_once '../../memory/trunk/db_cache.php';

/**
 * Abstract class for database access utlizing a caching mechanism
 * 
 * 
 */

abstract class db_base extends db_cache {	
	
	/**
	 * @access private
	 * @var resource - database link resource
	 */
	protected $link = NULL;

	/**
	 * @access private
	 */
	protected $db_host, $db_name, $db_user, $db_pass, $db_port;

	/**
	 * @access private
	 * @var string - data source connection string
	 */
	protected $connection_string;
	
	/**
	 * Default constructor
	 * 	  
	 * @param $connection_string (username:password@dbname.host:port)
	 */   	
	function __construct($connection_string, $cache_size = 0) {
		parent::__construct($cache_size);			

		$this->connection_string = $connection_string;

		list($this->db_user, $this->db_pass, $this->db_name, $this->db_host, $this->db_port) = preg_split("/:|@|\./", $connection_string);

		/* check that supplied port is a valid numeric value */
		if (! ereg("^([1-9][0-9]{1,4})", $this->db_port)) {		
			self::$errstr = "db_common_cache::__construct($connection_string) - invalid port: $db_port - value must be between 0 and 65535";		
		}
	}
	
	/**
	 * Return the database hostname
	 * 
	 * @return string
	 */
	public function get_db_host() { return $this->db_host; }
	
	/**
	 * Return the database port
	 * 
	 * @return int
	 */
	public function get_db_port() { return $this->db_port; }
	
	/**
	 * Return the database name
	 * 
	 * @return string
	 */
	public function get_db_name() { return $this->db_name; }
	
	/**
	 * Return the database user name
	 * 
	 * @return string
	 */
	public function get_db_user() { return $this->db_user; }
	
	/**
	 * Return the password used to connect to the database
	 * 
	 * @return string
	 */
	public function get_db_password() { return $this->db_pass; }	
	
	/**
	 * Return the data source connection string
	 * 
	 * @return string
	 */
	public function get_connection_string() { return $this->connection_string; }		
	
	 /* abstract functions to be defined by inheritting class */
	 abstract public function connect();
	 abstract public function disconnect();
	 abstract public function execute($sql, $reconnect = false);	
}

?>
