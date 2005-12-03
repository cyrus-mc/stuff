<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	$Author: $
	$Date: $
	$Revision: $
*/

define(__DEFAULTPASSWORDMODE__, "ENCRYPTED");

function __autoload($class_name) {
	   require_once 'dbase/' . $class_name . '.php';
}

// class used to store information about currently logged in user
class UserBean {
  	private $uname, $uid, $pmode = __DEFAULTPASSWORDMODE__;
	private $udata = array();
	private $database;

	public function setDB($db) {
		$this->database = $db;
	}
   /*
      Default constructor.

		Set username and userID
   */
	function __construct($uname, $uid) {
		$this->uname = $uname;
		$this->uid = $uid;
	}

	public function getUserName() { return $this->uname; } 

	public function getDatabase() { return $this->database; }

   /*
      Retrieve field from this array if it exists, else returns FALSE 
   */
	public function getValue($key) {
		if (isset($this->udata[$key])) {
			return $this->udata[$key];
		} else {
			return FALSE;
		}
	}

   /*
      Set the value associated with supplied key. If key doesn't exist
      it will be added.
   */
	public function setValue($key, $value) {
		$this->udata[$key] = $value;
	}

	/*
		Remove key and associated value from this.  If key doesn't exist
		return FALSE, else TRUE 
   */
	public function removeValue($key) {
		if (isset($this->udata[$key])) {
			unset($this->udata[$key]);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/* 
		Set the password mode (either ENCRYPTED or UNENCRYPTED)

		Parameter: mode
		values: e = ENCRYPTED, p = UNENCRYPTED
	*/
	public function setPasswordMode($mode) {
		if ($mode == "e") {
			$this->pmode = "ENCRYPTED";
		} elseif ($mode == "p") {
			$this->pmode = "UNENCRYPTED";
		} else {
			die("Invalid password mode : " . $mode);
		}
		return $this->pmode;
	}
	
	/*
		Set the user password (this is the database user password)
	*/
	public function setPassword($npasswd) {
		$sql = "ALTER USER " . $this->uname . " ENCRYPTED PASSWORD '" . $npasswd . "'";
	}

	/*
      Print the current values associated with the UserBean
   */
   public function printThis() {
		print_r($this);
		print_r($this->udata);
   }
}

// default permission level and redirect page
define(__REDIRECTPAGE__, "/access_denied.html");

/* 
	Class used to authenticate user on a page per page basis.
	pcontext = context of the page. This is compared with the uname to determine
		        permission
*/
class Authenticate {
   var $pcontext, $epage, $authorized = 1;
	var $ubean;

	/*
		Default constructor.  Sets page permission level and user permission
		level to this.
	*/
   function __construct($pcontext, $epage = __REDIRECTPAGE__) {
		if (strlen($pcontext) != 0) {
			$this->pcontext = substr($pcontext, 2);
		} else
			$this->pcontext = $pcontext;

		$this->epage = $epage;

		/* re-construct userBean, if it doesn't exist user has not logged in */
		$this->ubean = unserialize($_SESSION["ubean"]);
		if (!$this->ubean) {
			header("Location: " . $epage);
			session_destroy();
		}
      $this->checkAuth();
   }

	/*
		Check to see if user permissions are good enough to access this page
		(pcontext = uname)
	*/
   public function checkAuth() {
		if (strlen($this->pcontext) != 0 && $this->pcontext != $this->ubean->getUserName()) {
			$this->authorized = 0;
			header("Location: " . $this->epage);
		}
   }

	/*
		Retrieve the userBean associated with this authentication request
	*/
	public function getUserBean() {
		return $this->ubean;
	}

	/*
		Set the page context for this authentication request
	*/
	public function setPageContext($context) {
		$this->pcontext = $context;
	}

	/*
		Return the authentication flags (1 = auth, 0 = denied)
	*/
   public function getAuthorized() {
      return $this->authorized;
   }
}
?>
