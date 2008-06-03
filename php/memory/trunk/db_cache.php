<?php
/*
   vim:ts=3:sw=3:

   Implementation of base level class for database access with caching

   $Author: $
   $Date: $
   $Revision: $
*/

require_once 's_cache.php';

/**
 * Abstract class for database access utlizing a caching mechanism
 * 
 * 
 */

abstract class db_cache extends s_cache {	
		
	const GLOBAL_CACHE_LINE = 1;
	const USER_CACHE_LINE = 2;	
	 
	/**
	 * @access private
	 * @var array - mappings of tables to keys in cache (many -> many)
	 */
	private $table_to_key_mappings = array();
	private $key_to_table_mappings = array();	
	
	/**
	 * Parse a SQL select statement and return a string with the table names
	 * affected	  	
	 * 
	 * TODO: check on valid characters for table names (currently only alpha
	 * numeric characters are supported
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
			preg_match('/from\s+([[:alnum:]_]+(?:,\s+[[:alnum:]_]+)*)/', substr($sql, $last_index, $string_length), $out);			
			$start_index = $last_index + 5;
			$table_names .= $out[1];
		}
		return split(" ", str_replace(',', '', $table_names));
	}
	
	/**
	 * Parse a SQL update statement and return a string with the table names
	 * affected
	 * 
	 * TODO: check on valid characters for table names (currently only alpha
	 * numeric characters are supported
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
	 * TODO: check on valid characters for table names (currently only alpha
	 * numeric characters are supported
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
	 * TODO: check on valid characters for table names (currently only alpha
	 * numeric characters are supported
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
	 public function add($sql_hash, $data, array $table_names, $namespace, $overwrite = false) {	 		 	
	 	if ( ($return_value = parent::add($sql_hash, $data, $overwrite)) ) {	 			 			 		
	 		/* parse the statement and update the two maintained hash tables */
	 		foreach ($table_names as $table) {
	 			if (! isset($this->table_to_key_mappings[$table]))
	 				$this->table_to_key_mappings[$table] = array();
	 				
	 			$this->table_to_key_mappings[$table][$sql_hash] = $namespace;
	 			
	 			if (! isset($this->key_to_table_mappings[$sql_hash]))
	 				$this->key_to_table_mappings[$sql_hash] = array();
	 				
	 			$this->key_to_table_mappings[$sql_hash][$table] = $namespace;
	 		}	 		
	 	}
	 	return $return_value;	 	
	 }
	
	/**
	 * Remove mapping between key and table and vice versa
	 * 
	 * @param string $key
	 * @return boolean
	 */
	 public function remove($key) {	 	
	 	if ( ($return_value = parent::remove($key)) ) {
	 		foreach ($this->key_to_table_mappings[$key] as $table => $data)
	 			unset($this->table_to_key_mappings[$table][$key]);
	 				
			unset($this->key_to_table_mappings[$key]);			
	 	}
	 	return $return_value;
	 }
	 	 
	 /**
	  * Mark multiple cache lines dirty
	  * @param array keys
	  * @return void
	  */	  	
	 public function set_m_dirty(array $table_names, $namespace) {		
		foreach ($table_names as $table) {
			foreach (array_keys($this->table_to_key_mappings[$table]) as $key) {
				if ($this->table_to_key_mappings[$table][$key] == $namespace)					
					$this->set_dirty($key);
			}
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
}

?>
