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

	/* over-write sql_commands array if needed
	public function __construct($connection_string) {
		parent::__construct($connection_string);
		$this->sql_commands['create_database'] = "SQL COMMAND TO CREATE DB";
	}
	*/

	/* DEFINE ABSTRACT FUNCTIONS */

	/*
		Connect to specified database
   */
   public function connect() {
		/* pg_connect returns connection resource on success, FALSE on failure */
		$this->link = @pg_connect("host=".$this->db_host." dbname=".
		 			$this->db_name." user=".$this->db_user." password=".
					$this->db_pass." port=".$this->db_port);

		if ($this->link)
			return TRUE; /* connection successfull */
		
		return FALSE; /* connection failed */
	}

	public function query($sql, $reconnect=FALSE) {
		/* make sure we are connected and the connection status is good */
		if ($this->getStatus() == PGSQL_CONNECTION_BAD) {
			echo "re-establishing connection";
			if ($reconnect) {
					if (! $this->connect()) {
						echo "reconnect failed";
						return FALSE;
					}
			} else {
				echo "connection dead, reconnect = false";
				return FALSE;
			}
		}

		$this->sql_statement = $sql;
		$this->result = pg_query($this->link, $sql) or die("Error: " . pg_last_error());
		return $this->result;
	}

	/* END OF ABSTRACT FUNCTIONS */

	function getStatus() {
		if (! $this->link)
			return PGSQL_CONNECTION_BAD;

		return pg_connection_status($this->link);
	}

	/*
		Fetch row array and return reference to it
	*/
   function getRow() {
      $this->current_row = pg_fetch_assoc($this->result);
      return $this->current_row;
   }

	/*
		Seek to specified row in result set
	*/
   function seekRow($row = 0) { 
      if ($row >= 0) 
         return pg_result_seek($this->result, $row);
      else 
     		return FALSE; 
   }

	/*
		Throw away current result set (free memory)
	*/
   function freeResultSet() {
		if (get_resource_type($this->result) == "pgsql result")
      	return pg_free_result($this->result);
   }
	
	/*
		Disconnect from database
	*/
   function disconnect() {
      // clean up resources
      $this->freeResultSet();
      $this->current_row = NULL;

		if ($this->getStatus() == PGSQL_CONNECTION_OK)
      	return pg_close($this->link);
   }
}
?>
