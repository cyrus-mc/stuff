<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	$Author: cyrus $
	$Date: 2004/01/23 01:38:07 $
	$Revision: 1.4 $
*/

// class used to store information about currently logged in user
class UserBean {
   
   /*
      Default constructor.  Fields set in user_info are saved in this
   */
   function UserBean(&$user_info) {
      foreach ($user_info as $key => $value) {
	      $this->$key = $value;
      }
   }

   /*
      Retrieve field from this array if it exists, else returns FALSE 
   */
   function getField($field_name) {
   	if (isset($this->$field_name)) {
			return $this->$field_name;
		} else {
			return FALSE;
		}
   }

   /*
      Change value associated with supplied key. If key doesn't exist
      it will be added. Returns new value
   */
   function setField($key, $value) {
		$this->$key = $value;
		return $this->$key;
   }

	/*
		Remove key and associated value from this.  If key doesn't exist
		return FALSE, else TRUE 
   */
	function removeField($key) {
		if (isset($this->$key)) {
			unset($this->$key);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/*
      Print the current value associated with the UserBean
   */
   function printThis() {
		print_r($this);
   }
}

// default permission level and redirect page
define(__DEFAULTPERMISSION__, 100);
define(__REDIRECTPAGE__, "error.html");

/* 
	Class used to authenticate user on a page per page basis.
	plevel = permission level required to access this page
	ulevel = users permission level
*/
class Authenticate {
   var $pageLevel, $userLevel, $authorized;

	/*
		Default constructor.  Sets page permission level and user permission
		level to this.
	*/
   function Authenticate($plevel = __DEFAULTPERMISSION__, $ulevel = 0) {
      $this->pageLevel = $plevel;
      $this->userLevel = $ulevel;
      $this->check();
   }

	/*
		Check to see if user permissions are good enough to access this page
		(pageLevel = userLevel)
	*/
   function check() {
      if ($this->pageLevel == $this->userLevel) {
         $this->authorized = 1;
      } else {
         $this->authorized = 0;
      }
      // return authorized value
      return $this->authorized;
   }

	/*
	*/
	function setPagePerm($value) {
		$this->pageLevel = $value;
	}

	/*
	*/
	function setUserPerm($value) {
		$this->userLevel = $value;
	}
	
	/*
		Return the authentication flags (1 = auth, 0 = denied)
	*/
   function getAuthorized() {
      return $this->authorized;
   }

	/*
		Redirect user if authorixation denied
	*/
   function reDirect() {
     if (!$this->authorized) {
        header("Location: " . __REDIRECTPAGE__);
     }
   }
}
?>
