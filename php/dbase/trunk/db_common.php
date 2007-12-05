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
   protected $db_host, $db_name, $db_user, $db_pass, $db_port;
   protected $connection_string, $sql_statement;

	// common database specific SQL commands
	protected $sql_commands = array('create_database' => 'CREATE DATABASE %s',
									'drop_database' => 'DROP DATABASE %s',
									'create_user' => ' CREATE_USER %s WITH PASSWORD %s',
									'drop_user' => 'DROP USER %s');
   /*
      Default constructor.

		String format - username:password@dbname.host:port
   */
	function __construct($connection_string) {

		$this->connection_string = $connection_string;

		list($this->db_user, $this->db_pass, $this->db_name, $this->db_host, $this->db_port) = preg_split("/:|@|\./", $connection_string);

		// check supplied port is a numeric value
		if (! ereg("^([1-9][0-9]{1,4})", $this->db_port))
			die("Invalid port: " . $db_port . " (value must be between 0 and 65535)");
	}

	abstract public function connect();
	abstract public function query($sql); 
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
   public function getConnectionString() {
		return $this->connection_string;
   }

   /*
      Set the current SQL statement
   */
   public function setSQL($sql) {
      $this->sql_statement = $sql;
   }

	/* execute common SQL commands */
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
	/* end common SQL commands */
}

?>
