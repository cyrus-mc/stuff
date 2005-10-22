<?php
/*
   vim:ts=3:sw=3:

   Implementation of MySQL database access class.

   $Author: cyrus $
   $Date: 2004/01/22 21:15:07 $
   $Revision: 1.3 $
*/

// include base class
include 'db_common.php';

class DB_postgres extends DB_common {
   
   var $link = NULL; // link resource identifier
   var $result = NULL; // result set resource
   var $current_row = NULL; // associative array of row from result set

   /*
      Default constructor.  Parameters have default values so are not
      required when constructing
   */
   function DB_postgres($db_host = NULL, $db_port = NULL, $db_name = NULL,
                        $db_user = NULL, $db_pass = NULL) {
		// call base class constructor
      $this->DB_common($db_host, $db_port, $db_name, $db_user, $db_pass);
   }

	/*
		Connect to specified database or die (will change this)
   */
   function connect() {
		 $this->link = pg_connect("host=".$this->db_host." dbname=".
		 			$this->db_name." user=".$this->db_user." password=".
					$this->db_pass." port=".$this->db_port)
					or die("can not connect to server");
   }

	/*
		Execute current SQL statement and return reference to result or
		die if query fails (will change this)
	*/
   function &executeSQL() {
      $this->result = mysql_query($this->sql_statement, $this->link) or 
                   die("Query failed : " . mysql_error());
      return $this->result;
   }

	/*
		Fetch row array and return reference to it
	*/
   function &getRow() {
      $this->current_row = mysql_fetch_array($this->result, MYSQL_ASSOC);
      return $this->current_row;
   }

	/*
		Seek to specified row in result set
	*/
   function seekRow($row = 0) { 
      if ($row >= 0) 
         mysql_data_seek($this->result, $row);
      else 
         echo "Negative seek value";
      
   }

	/*
		Throw away current result set (free memory)
	*/
   function freeResultSet() {
		if ($this->result)
      	pg_free_result($this->result);
   }
	
	/*
		Disconnect from database
	*/
   function disconnect() {
      // clean up resources
      $this->freeResultSet();
      $this->current_row = NULL;

		$stat = pg_connection_status($this->link);
		if ($stat == PGSQL_CONNECTION_OK)
      	pg_close($this->link);
   }
}
?>
