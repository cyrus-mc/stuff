<?php
/*
   vim:ts=3:sw=3:

   Implementation of PostgreSQL database access class. Inherits base db_common
	class.

   $Author: $
   $Date: $
   $Revision: $
*/

// include base class
require_once 'db_common_cache.php';

class db_postgres extends db_common_cache {
	
	/* define the parent class abstract functions */
	
	/**
	 * Definition of abstract connect() used to connect to the data source
	 * 
	 * @return boolean
	 */
	public function connect() {
		
		/* pg_connect returns connection resource on success, FALSE on failure */
		$this->link = @pg_connect("host=" . $this->db_host . " dbname=" .
				$this->db_name . " user=" . $this->db_user . " password =" .
				$this->db_pass . " port=" . $this->db_port);
								
		/* check status of link */
		if ($this->link)
			return true; /* connection successfull */
			
		/* set error string */
		self::$errstr = "db_postgres::__construct($this->connection_string) - failed to connect to the database resource";
		return false;		
	}
	
	/**
	 * Execute an SQL statement
	 * 
	 * @param string $sql
	 * @param boolean $reconnect
	 * @return mixed
	 */
	public function execute($sql, $reconnect = FALSE) {
		/* make sure we are connected and the connection status is good */
		if ($this->get_status() == PGSQL_CONNECTION_BAD) {
			if ($reconnect && ! $this->connect())
				self::$errstr = "db_postgres::execute(.., $reconnect) - failed to re-connect to the database resource";				
			else
				self::$errstr = "db_postgres::execute(.., $reconnect) - connection to resource dead, reconnect not specified";
			return false;			
		}		
		
		/* create the cache key */
		$sql_hash = md5($sql);
		/* determine type of SQL statement (select, update, insert or other) */
		$type_of_stmt = "execute_" . substr($sql, 0, strpos($sql, ' '));		
		
		return $this->$type_of_stmt($sql, $sql_hash);					
	}
	
	/**
	 * Execute a SELECT SQL statement
	 * 
	 * @param string $sql
	 * @param string $sql_hash
	 * @return boolean 
	 */
	public function execute_select($sql, $sql_hash) {
		/* check if result is cached */
		$cache = $this->get($sql_hash);
				
		if ($cache && ! $cache['dirty']) {
			print "in cache\n";			
			return $cache['contents'];
		}

		/* execute the query since it was either not in the cache or dirty */
		$result = pg_query($this->link, $sql);				
		if (result) {
			/* if it was in cache but dirty bit was set, restore */
			if ($cache) {
				print "in cache but dirty\n";					
				$this->set($sql_hash, $result);	
			} else {
				print "not in cache\n";
				$this->add($sql_hash, $result, $this->parse_select($sql), false);
			}			
							
			return $result;
		}		
		self::$errstr = "db_postgres::execute_select($sql, ...) - failed to execute :: " . pg_last_error($this->link);
		return false;		
	}
	
	/**
	 * Execute an INSERT SQL statement
	 * 
	 * @param string $sql
	 * @param string $sql_hash
	 * @return boolean
	 */
	public function execute_insert($sql, $sql_hash) {
		$result = pg_query($this->link, $sql);
		
		/* check if insert was successfull, if so, mark affected cache lines dirty */		
		if (result)
			if (pg_affected_rows($result) != 0)				
				$this->set_m_dirty($this->parse_insert($sql));			
			return $result;
		
		self::$errstr = "db_postgres::execute_insert($sql, ...) - failed to execute :: " . pg_last_error($this->link);
		return $false;
	}
	
	/**
	 * Execute an UPDATE SQL statement
	 * 
	 * @param string $sql
	 * @param string $sql_hash
	 * @return boolean
	 */
	public function execute_update($sql, $sql_hash) {
		$result = pg_query($this->link, $sql);
		
		/* check if update was successfull, if so, mark affected cache lines dirty */
		if (result)
			if (pg_affected_rows($result) != 0)
				$this->set_m_dirty($this->parse_update($sql));			
			return $result;
		
		self::$errstr = "db_postgres::execute_update($sql, ...) - failed to execute :: " . pg_last_error($this->link);
		return false;
	}
	
	/**
	 * Execute a DROP SQL statement
	 * 
	 * @param string $sql
	 * @param $sql_hash
	 * @return boolean
	 */
	public function execute_delete($sql, $sql_hash) {
		$result = pg_query($this->link, $sql);
		
		/* check if drop was successfull, if so, mark affected cache lines dirty */
		if (result)
			if (pg_affected_rows($result) != 0)
				$this->set_m_dirty($this->parse_delete($sql));
			return $result;
			
		self::$errstr = "db_postgres::execute_delete($sql, ...) - failed to execute :: " . pg_last_error($this->link);
		return false;
	}
	
	/**
	 * Disconnect from the database resource
	 * 
	 * @return boolean
	 */
	public function disconnect() {
		if ($this->get_status() == PGSQL_CONNECTION_OK)
			return pg_close($this->link);		
	}
	
	/* end define the parent class abstract functions */

	/**
	 * Query the database connection status
	 * 
	 * @return int
	 */
	public function get_status() {
		if (! $this->link)
			return PGSQL_CONNECTION_BAD;
			
		return pg_connection_status($this->link);
	}		
}
?>
