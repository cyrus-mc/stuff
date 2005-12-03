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
require_once 'db_common.php';

class db_postgres extends db_common {
   
   private $result = NULL; // result set resource
   private $current_row = NULL; // associative array of row from result set

	// common database specific SQL commands
	private $sql_commands = array('create_database' => 'CREATE DATABASE %s',
    		 		     		    'drop_database' => 'DROP DATABSE %s',
	   							 'create_user' => 'CREATE USER %s WITH PASSWORD %s',
	   							 'drop_user' => 'DROP USER %s');

	/* default construction */
	//function __construct($db_host, $db_port, $db_name, $db_user, $db_pass) {
		// call parent constructor
   //	parent::__construct($db_host, $db_port, $db_name, $db_user, $db_pass);
	//}

	/* DEFINE ABSTRACT FUNCTIONS */

	/*
		Connect to specified database or die (will change this)
   */
   public function connect() {
		 $this->link = @pg_connect("host=".$this->db_host." dbname=".
		 			$this->db_name." user=".$this->db_user." password=".
					$this->db_pass." port=".$this->db_port);
	}

	public function query($sql) {
		if ($this->link == FALSE)
			die('** INVALID CONNECTION **');

		$this->sql_statement = $sql;
		$this->result = pg_query($this->link, $sql) or die("Error: " . pg_last_error());
		return $this->result;
	}

	public function createDB($name) {
		$this->query(sprintf($this->sql_commands['create_database'], $name));
	}

	public function dropDB($name) {
		$this->query(sprintf($this->sql_commands['drop_database'], $name));
	}

	public function createUser($name, $pword) {
		$this->query(sprintf($this->sql_commands['create_user'], $name, $pword));
	}

	public function dropUser($name) {
		$this->query(sprintf($this->sql_commands['drop_user'], $name));
	}

	/* END OF ABSTRACT FUNCTIONS */

	function getStatus() {
		if ($this->link)
			return TRUE;
		else
			return FALSE;
	}

	/*
		Fetch row array and return reference to it
	*/
   function &getRow() {
      $this->current_row = pg_fetch_assoc($this->result);
      return $this->current_row;
   }

	/*
		Seek to specified row in result set
	*/
   function seekRow($row = 0) { 
      if ($row >= 0) 
         pg_result_seek($this->result, $row);
      else 
         echo "Negative seek value";
      
   }

	/*
		Throw away current result set (free memory)
	*/
   function freeResultSet() {
		if (get_resource_type($this->result) == "pgsql result")
      	pg_free_result($this->result);
   }
	
	/*
		Disconnect from database
	*/
   function disconnect() {
      // clean up resources
      $this->freeResultSet();
      $this->current_row = NULL;

		if (get_resource_type($this->link) == "pgsql link")
      	pg_close($this->link);
   }
}
?>
