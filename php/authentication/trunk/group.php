<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	@author: Matthew Ceroni
	@version: 1.0	
	
	@package authentication
*/

class Group {
	
	/**	 
	 * @access private
	 * @var string - string representation of object
	 */
	private static $object_string = "group_object";
	
	/**
	 * @access private
	 * @var string - the group name
	 * @var int - the group ID
	 */	
	private $groupname, $gid;
	
	/**
	 * Default constructor
	 *
	 * @param string $name
	 * @param int $id
	 */
	public function __construct($name, $id) {
		$this->groupname = $name;
		$this->gid = $id;
	}
		
	/**
	 * Return the ID for this group
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->gid;
	}
	
	/**
	 * Return the name for this group
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->groupname;
	}
	
	/**
	 * Magic function : returns strings representation of object
	 *
	 * @return string
	 */
	public function __toString() { return self::$object_string; }
}
?>