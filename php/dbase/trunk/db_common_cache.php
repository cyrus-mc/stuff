<?php
/*
   vim:ts=3:sw=3:

   Implementation of base level class for database access with caching

   $Author: $
   $Date: $
   $Revision: $
*/

require_once '../../memory/trunk/s_cache.php';

/**
 * Abstract class for database access utlizing a caching mechanism
 * 
 * 
 */

abstract class db_common_cache extends s_cache {	
	
	const GLOBAL_CACHE_LINE = 1;
	const USER_CACHE_LINE = 2;
	
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
	 * @access private
	 * @var array - mappings of tables to keys in cache (many -> many)
	 */
	private $table_to_key_mappings = array();
	private $key_to_table_mappings = array();
	
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
	
	/**
	 * Parse a SQL select statement and return a string with the table names
	 * affected	  	
	 * 
	 * TODO: check on valid characters for table names
	 * 
	 * @param string $sql
	 * @return array
	 */
	protected function parse_select($sql) {		
		$start_index = 0;
		$last_index = 0;
		$string_length = strlen($sql);
		$table_names = "";
		while (($last_index = stripos($sql, 'from ', $start_index))) {		
			preg_match('/from\s+([[:alnum:]]+(?:,\s[[:alnum:]]*)?\s?).*/', substr($sql, $last_index, $string_length), $out);
			$start_index = $last_index + 5;			
			$table_names .= $out[1];
		}
		return split(" ", str_replace(',', '', $table_names));		
	}
	
	/**
	 * Parse a SQL update statement and return a string with the table names
	 * affected
	 * 
	 * TODO: check on valid characters for table names
	 * 
	 * @param string $sql
	 * @return array
	 */
	protected function parse_update($sql) {		
		preg_match('/update\s+([[:alnum:]]+)\s+.*/', $sql, $out);		
		return split(" ", $out[1]);				
	}
	
	/**
	 * Parse a SQL insert statement and return a string with the table names
	 * affected
	 * 
	 * TODO: check on valid characters for table names
	 * 
	 * @param string $sql
	 * @return array
	 */
	protected function parse_insert($sql) {		
		preg_match('/insert\s+into\s+([[:alnum:]]+)\s+.*/', $sql, $out);
		return split(" ", $out[1]);			
	}
	
	/**
	 * Parse a SQL delete statement and return a string with the table names
	 * affected
	 * 
	 * TODO: check on valid characters for table names
	 * 
	 * @param string $sql
	 * @return array
	 */
	protected function parse_delete($sql) {		
		preg_match('/delete\s+from\s+([[:alnum:]]+)\s+.*/', $sql, $out);
		return split(" ", $out[1]);			
	}
	
	/**
	 * Add element to table_to_key_mappings and key_to_table_mappings list
	 * 
	 * @param string $sql_hash
	 * @param mixed $data
	 * @param array $table_names
	 * @param boolean $overwrite
	 * @return boolean 
	 */
	 public function add($sql_hash, $data, array $table_names, $overwrite = false) {
	 	/* incase the add of parent function removes an element */
	 	$key_to_remove = $this->get_oldest_cache();
	 	
	 	if (parent::add($sql_hash, $data, $overwrite)) {	 			 			 		
	 		/* parse the statement and update the two maintained hash tables */
	 		foreach ($table_names as $table) {
	 			$this->table_to_key_mappings[$table][$sql_hash] = self::GLOBAL_CACHE_LINE;
	 			$this->key_to_table_mappings[$sql_hash][$table] = self::GLOBAL_CACHE_LINE;
	 		}	 			 	
	 		return true;
	 	}
	 	return false;	 	
	 }
	
	/**
	 * Remove mapping between key and table and vice versa
	 * 
	 * @param string $key
	 * @return void
	 */
	 public function remove($key) {	 	
	 	if (parent::remove($key)) {
	 		foreach ($this->key_to_table_mappings[$key] as $table => $data)
	 			unset($this->table_to_key_mappings[$table][$key]);
	 				
			unset($this->key_to_table_mappings[$key]);
			return true;	
	 	}
	 	return false;	 	
	 }

	 /**
	  * Mark multiple cache lines dirty
	  * @param array keys
	  * @return void
	  */	  	
	 public function set_m_dirty(array $table_names) {		
		foreach ($table_names as $table) {							
			parent::set_m_dirty(array_keys($this->table_to_key_mappings[$table]));
		}
	 }	
	 
	 /**
	  * Print contents of the table to key map
	  * 
	  * @return void
	  */
	 public function print_table_to_key_cache() { print_r($this->table_to_key_mappings); }
	 
	 /**
	  * Print the contents of the key to table map
	  * 
	  * @return void
	  */
	 public function print_key_to_table_cache() { print_r($this->key_to_table_mappings); }
	
	 /* abstract functions to be defined by inheritting class */
	 abstract public function connect();
	 abstract public function disconnect();
	 abstract public function execute($sql, $reconnect = false);	
}

?>
