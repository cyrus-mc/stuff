<?php
/*
   vim:ts=3:sw=3:

   Implementation of base level class for database access

   $Author: $
   $Date: $
   $Revision: $
*/

abstract class db_common {

	protected $link = NULL; // link resource identifier
   protected $db_host, $db_name, $db_user, $db_pass;
   protected $sql_statement;

   /*
      Default constructor.  Paramenters have default values so are not
      required when constructing
   */
	function __construct($db_host, $db_port, $db_name, $db_user, $db_pass) {
		$this->db_host = $db_host;
		$this->db_name = $db_name;
		$this->db_user = $db_user;
		$this->db_pass = $db_pass;
		// check supplied port is a numeric value
		if (ereg("^([1-9][0-9]*$)", $db_port))
			$this->db_port = $db_port;
		else
			die("Invalid port: " . $db_port . " (value must be between 0 and 65535)");
	}

	abstract public function connect();
	abstract public function query($sql); 
	abstract public function createDB($name);
	abstract public function dropDB($name);
	abstract public function createUser($name, $pword);
	abstract public function dropUser($name);
	abstract public function disconnect();

   /*
      Retrieve host name
   */
   public function getDBhost() {
      return $this->db_host;
   }

   /*
      Retrieve database name
   */
   public function getDBname() {
      return $this->db_name;
   }

   /*
      Retrieve username
   */
   public function getDBuser() {
      return $this->db_user;
   }

   /*
      Retrieve password
   */
   public function getDBpassword() {
      return $this->db_pass;
   }

   /*
      Retrieve port number
   */
   public function getDBport() {
      return $this->db_port;
   }

   /*
      Retrieve the current SQL statement
   */
   public function getSQL() {
      return $this->sql_statement;
   }

   /*
      Return database connection string
   */
   public function getConnectString() {
      // form the connection string
      $str = $this->db_user . ":" . $this->db_pass . "@" . $this->db_name
             . "." . $this->db_host . ":" . $this->db_port;
      return $str; 
   }

   /*
      Set the current SQL statement
   */
   public function setSQL($sql) {
      $this->sql_statement = $sql;
   }
}

?>
