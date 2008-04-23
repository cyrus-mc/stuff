<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	$Author: $
	$Date: $
	$Revision: $
*/

function __autoload($class_name) {
	   require_once 'dbase/' . $class_name . '.php';
}

// default permission level and redirect page
define(__REDIRECTPAGE__, "/access_denied.html");

/* 
	Class used to authenticate user on a page per page basis.

	** note: singleton class - can not be instantiated with new
*/
class Authentication {

	private $permissions = array(), $ref_count;
	private static $instance = null;

	private static $object_string = "authentication_object";

	/*
		Default constructor

		** note: private so that class can not be instantiated by new
	*/
	private function __construct($permissions) {
		$this->ref_count = 0;

		/* set the permissions */
		$this->set($permissions, TRUE);
	}

	/* 
		The singleton method
				      
		Returns only one instance of the class
	*/
	public static function getInstance($permissions = null) {

		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c($permissions);
		}
		/* increment reference count */
		self::$instance->ref_count++;

		return self::$instance;
	}

	/* 
		Release object. Clear memory if ref_count = 0
	*/
	public function release($clear = FALSE) {
		/* decrement ref_count */
		$this->ref_count--;

		if ($this->ref_count == 0 || $clear_mem) {
			echo "Clearing permissions array\n";
			$this->clear();
		}
	}

	/* 
		Clear the permissions array
	*/
	public function clear() {
		foreach (array("application", "content", "user_id", "group_id") as $label) {
			unset($this->permissions[$label]);
			$this->permissions[$label] = array();
		}
	}

	/* 
		Set the permissions array
	*/
	public function set($permissions, $clear = FALSE) {
		/* clear first if flag is set */
		if ($clear)
			$this->clear();

		/* loop over rows of supplied argument and add to appropriate array */
		foreach ($permissions as $row) {
			$this->permissions["application"][] = current($row);
			$this->permissions["content"][] = next($row);
			$this->permissions["user_id"][] = next($row);
			$this->permissions["group_id"][] = next($row);
		}
	}

	/* 
		Check access
	*/
	//public check($application, $content, $user_id, $group_id) {
	//}

	public function get() {
		return $this->ref_count;
	}

	public function __toString() { return self::$object_string; }
}
?>
