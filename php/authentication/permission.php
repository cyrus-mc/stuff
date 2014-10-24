<?php
/*
	vim:ts=3:sw=3:

	Implementation of classes used for storage of user information and page 
	level access control

	@author: Matthew Ceroni
	@version: 1.0	
	
	@package authentication
*/

require_once 'user.php';

class Permission {

	/**
	 * Permission classes
	 */
	const CLASS_USER = 'user';
	const CLASS_GROUP = 'group';
	const CLASS_OTHER = 'other';
	
	/**
	 * Permission types
	 */
	const TYPE_READ = 'r';
	const TYPE_WRITE = 'w';
	const TYPE_EXEC = 'x';
	/** 
	 * @access private
	 * @var string - string representation of object 
	*/	 
	private static $object_string = "permission_object";
		
	/**
	 * @access private
	 * @var string - resource name
	 * @var string|octal - permission sstring
	 */
	private $resource_name, $permission_string = null;
	private $user_object = null;
	
	/**
	 * @access private
	 * @var array - array used to hold the resource permissions (user, group and other)
	 */
	private $permissions_hash = array('user' => array('r' => false, 'w' => false, 'x' => false),
		'group' => array('r' => false, 'w' => false, 'x' => false),
		'other' => array('r' => false, 'w' => false, 'x' => false));	
	
	/**
	 * Default constructor
	 *
	 * @param string $name
	 * @param User $owner
	 * @param string $pstring
	 * @param boolean $octal
	 */
	public function __construct($name, User $owner, $pstring, $octal = false) {
		$this->resource_name = $name;		
		
		$this->user_object = $owner;		
		
		/* validate that supplied pstring is a valid permissions string */
		/* valid pstring = rwxr-x--x or some valid permutation */
		/* valid pstring = 752 or some valid permutation */
		$this->set_permission($pstring, $octal);				
	}
	
	/**
	 * Reset all permissions to false for this resource
	 *
	 * @access private
	 */
	private function reset_hash() {
		$this->permissions_hash['user']['r'] = false;
		$this->permissions_hash['user']['w'] = false;
		$this->permissions_hash['user']['x'] = false;
		$this->permissions_hash['group']['r'] = false;
		$this->permissions_hash['group']['w'] = false;
		$this->permissions_hash['group']['x'] = false;
		$this->permissions_hash['other']['r'] = false;
		$this->permissions_hash['other']['w'] = false;
		$this->permissions_hash['other']['x'] = false;
	}
	
	/**
	 * Validate the supplied permission string
	 *
	 * @access private
	 * @param string $pstring
	 * @param boolean $octal
	 * @return boolean
	 */
	private function validate_pstring($pstring, $octal) {
		if ($octal) {
			/* supplied string is in octal form */
			return preg_match('/^[0-7]{3,}$/', $pstring);				
		} else {
			/* supplied string is in text form */
			return preg_match('/^((r|-)(w|-)(x|-)){3,}$/', $pstring);					
		}
	}

	/**
	 * Set the permissions for this resource
	 *
	 * @param string $pstring
	 * @param boolean $octal
	 * @return boolean
	 */
	public function set_permission($pstring, $octal) {
				
		if (!$this->validate_pstring($pstring, $octal)) {
			return false;
		}		
		/* set the permissions */
		$this->permission_string = $pstring;
			
		$this->reset_hash();
		
		/* parse the string and set the permissions_hash */			
		foreach (array_keys($this->permissions_hash) as $permission_type) {
			if ($octal) {
				/* convert decimal to octal */				
				$bit = str_split(sprintf("%03s", base_convert($pstring[0], 8, 2)));					
				$this->permissions_hash[$permission_type]['r'] = $bit[0];
				$this->permissions_hash[$permission_type]['w'] = $bit[1];
				$this->permissions_hash[$permission_type]['x'] = $bit[2];									
			} else {
				/* iterate over each character of the permission string */				
				for ($i = 0; $i < 3; $i++) {
					if ($pstring[0] != '-') {						
						/* permission is on, so set it */
						$this->permissions_hash[$permission_type][$pstring[0]] = true;
					}						
				}		
			}		
			/* eat up each character as we process it */
			$pstring = substr($pstring, 1);												
		} 
		return true;		
				
	}	
	
	/**
	 * Return the owner of this resource
	 *
	 * @return string
	 */
	public function get_owner() { return $this->$owner; }
	
	/**
	 * Return the group owner of this resource
	 *
	 * @return string
	 */
	public function get_group() { return $this->$group; }	
	
	/**
	 * Check permission
	 *
	 * @param string $class
	 * @param string $type
	 * @return boolean
	 */
	private function check_permission($class, $type) {	
		$return_value = $this->permissions_hash[$class][$type];
		
		if (isset($return_value)) {
			return $return_value;
		} else {
			return false;
		}
	}
	
	/**
	 * Check if user has read permission to resource
	 *
	 * @return boolean
	 */
	public function user_has_read() {
		return $this->check_permission(self::CLASS_USER, self::TYPE_READ);
	}
	
	/**
	 * Check if user has write permission to resource
	 *
	 * @return boolean
	 */
	public function user_has_write() {
		return $this->check_permission(self::CLASS_USER, self::TYPE_WRITE);
	}
	
	/**
	 * Check if user has execute permission to resource
	 *
	 * @return boolean
	 */
	public function user_has_execute() {
		return $this->check_permission(self::CLASS_USER, self::TYPE_EXEC);
	}
	
	/**
	 * Check if group has read permission to resource
	 *
	 * @return boolean
	 */
	public function group_has_read() {
		return $this->check_permission(self::CLASS_GROUP, self::TYPE_READ);
	}
	
	/**
	 * Check if group has write permission to resource
	 *
	 * @return boolean
	 */
	public function group_has_write() {
		return $this->check_permission(self::CLASS_GROUP, self::TYPE_WRITE);
	}
	
	/**
	 * Check if group has execute permission to resource
	 *
	 * @return boolean
	 */
	public function group_has_execute() {
		return $this->check_permission(self::CLASS_GROUP, self::TYPE_EXEC);
	}
	
	/**
	 * Check if other has read permission to resource
	 *
	 * @return boolean
	 */
	public function other_has_read() {
		return $this->check_permission(self::CLASS_OTHER, self::TYPE_READ);
	}
	
	/**
	 * Check if other has write permission to resource
	 *
	 * @return boolean
	 */
	public function other_has_write() {
		return $this->check_permission(self::CLASS_OTHER, self::TYPE_WRITE);
	}
	
	/**
	 * Check if other has execute permission to resource
	 *
	 * @return boolean
	 */
	public function other_has_execute() {
		return $this->check_permission(self::CLASS_OTHER, self::TYPE_EXEC);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param User $user_object
	 */
	public function has_read(User $user_object) {
		/* TODO */
	}	
	
	public function has_write(User $user_object) {
		/* TODO */
	}
	
	public function has_execute(User $user_object) {
		/* TODO */
	}
}
?>