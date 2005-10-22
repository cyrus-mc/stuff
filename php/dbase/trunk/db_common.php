<?php
/*
   vim:ts=3:sw=3:

   Implementation of base level class for database access

   $Author: cyrus $
   $Date: 2004/01/22 21:14:35 $
   $Revision: 1.3 $
*/

// define some defaults
define(__DEFAULTHOST__, "localhost");
define(__DEFAULTPORT__, 3306);
define(__DEFAULTDB__, "mysql");
define(__DEFAULTUSER__, "root");
define(__DEFAULTPASS__, "nothing");

class DB_common {
	
   var $db_host, $db_name, $db_user, $db_pass;
   var $sql_statement;

   /*
      Default constructor.  Paramenters have default values so are not
      required when constructing
   */
   function DB_common($db_host = __DEFAULTHOST__, $db_port = __DEFAULTPORT__,
                      $db_name = __DEFAULTDB__, $db_user = __DEFAULTUSER__, 
                      $db_pass = __DEFAULTPASS__) {
      $this->db_host = $db_host;
      $this->db_name = $db_name;
      $this->db_pass = $db_pass;
      $this->db_user = $db_user;

      // check supplied port is a numeric value (or atleast starts with one)
      if (ereg("^([1-9][0-9]*)", $db_port)) 
         $this->db_port = $db_port;
      else {
         //echo "Supplied port not number, setting to default (3306)\n";
         $this->db_port = __DEFAULTPORT__;
      }
   }

   /*
      Retrieve host name
   */
   function getHost() {
      return $this->db_host;
   }

   /*
      Retrieve database name
   */
   function getDBName() {
      return $this->db_name;
   }

   /*
      Retrieve username
   */
   function getUser() {
      return $this->db_user;
   }

   /*
      Retrieve password
   */
   function getPassword() {
      return $this->db_pass;
   }

   /*
      Retrieve port number
   */
   function getPort() {
      return $this->db_port;
   }

   /*
      Retrieve the current SQL statement
   */
   function getSQL() {
      return $this->sql_statement;
   }

   /*
      Return database connection string
   */
   function getConnectString() {
      // form the connection string
      $str = $this->db_user . ":" . $this->db_pass . "@" . $this->db_name
             . "." . $this->db_host . ":" . $this->db_port;
      return $str; 
   }

   /*
      Set the current SQL statement
   */
   function setSQL($sql) {
      $this->sql_statement = $sql;
   }
}

?>
