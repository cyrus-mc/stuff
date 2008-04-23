<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	@author: Matthew Ceroni
	@version: 1.0	
	
	@package authentication

 * include the group class
 */
require_once 'group.php';

/**
 * Class representation of a user.
 */
class User {
	
	/** 
	 * @access private
	 * @var string - string representation of object 
	*/
	private static $object_string = "user_object";
	
	/**
	 * @access private
	 * @var string - the username
	 * @var int - the user ID	 
	 */
	private $username, $uid;	 
		
	/**	 
	 * @access private
	 * @var array - list of grups user belongs to by id
	 */
	private $group_list_by_id = array();
	
	/**	 	
	 * @access private
	 * @var array - list of groups user belongs to by name
	 */	
	private $group_list_by_name = array();
	
	/**
	 * Default constructor
	 *
	 * @param string $name - the username
	 * @param int $id - the user ID
	 * @param array $groups - list of groups user belongs to
	 */
	public function __construct($name, $id, array $groups = null) {
		$this->username = $name;
		$this->uid = $id;

		# verify that the supplied groups variable is an array if not null
		if (! is_null($groups)) {
			if (is_array($groups)) {						
				# loop over array and create each group object				
				foreach ($groups as $key => $value) {					
					$this->group_list_by_id[$key] = new Group($value, $key);
					$this->group_list_by_name[$value] = & $this->group_list_by_id[$key];
				}
			} else {
				print "error: supplied groups variable is not an array";
			}
		}
	}

	/**
	 * Return the UID for this user
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->uid;
	}
	
	/**
	 * Return the username for this user
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->username;
	}
	
	/**
	 * Check if user is member of specified group by group ID
	 *
	 * @param int $gid
	 * @return boolean
	 */
	public function is_member_by_id($gid) {
		return isset($this->group_list_by_id[$gid]);
	}
	
	/**
	 * Check if user is member of specified group by group name
	 *
	 * @param string $gname
	 * @return boolean
	 */
	public function is_member_by_name($gname) {
		return isset($this->group_list_by_name[$gname]);
	}
		
	/**
	 * helper function: print groups user belongs to
	 *
	 */
	public function list_groups() {
		print "User is part of the following groups: \n";
		foreach($this->group_list_by_id as $key => $value) {
			print "\t" . $value->get_name() . " (gid = " . $value->get_id() . ")\n";		
		}
	}
		
	/**
	 * Print $this object
	 *
	 */
	public function print_self() { print_r($this); }
	
	/**
	 * Magic function : returns strings representation of object
	 *
	 * @return string
	 */
	public function __toString() { return self::$object_string; }
}
?>