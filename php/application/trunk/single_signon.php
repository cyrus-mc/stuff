<?php
/*
	vim:ts=3:sw=3:

	Implementation of Single Sign-on
   
	$Author: $
	$Date: $
	$Revision: $	
*/

require_once 'dbase/db_base.php';
require_once 'authentication/user.php';

class single_signon {
		
	/**
	 * @access private
	 * @var int - holds the login date and time
	 */
	private $login_time;
	
	/**
	 * @access private
	 * @var int - number of seconds login is valid
	 */
	private $valid_time;
	
	/**
	 * @access private
	 * @var int - time which login is valid until
	 */
	private $login_valid_till;
	
	/**
	 * @access private
	 * @var object - user object
	 */
	private $user;
	
	/**	 
	 * @access private
	 * @var array - list of applications, by id
	 */
	private $app_list_by_id = array();
	
	/**	 	
	 * @access private
	 * @var array - list of applications, by name
	 */	
	private $app_list_by_name = array();
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	protected $errstr = "";
			
	/**
	 * Default constructor. 
	 * Initialize application space if not already done
	 * 
	 * @param string $username
	 * @param string $password
	 * @param int $valid_time
	 * @return void
	 */
	public function __construct($username, $password, $db_handle, $valid_time = 0) {		
		/* set the login time */		
		$this->login_time = time();
		$this->valid_time = $valid_time;
		$this->login_valid_till = $this->login_time + $valid_time;
		
		/* check users credentials */
		$login_result = $db_handle->execute("SELECT ss_users.uid, u_groups.gid, g_name FROM u_groups, ss_groups, ss_users 
			WHERE ss_groups.gid = u_groups.gid AND ss_users.uid = u_groups.uid AND ss_users.u_name = '$username'
			AND ss_users.p_word = '$password'");
					
		if ($login_result && count($login_result)) {
			$this->user = new user($username, $login_result[0]['uid']);
			/* populate the users group */
			foreach ($login_result as $group)
				$this->user->add_group($group['gid'], $group['g_name']);
							
			/* query the applications user is allowed to user */
			$valid_apps = $db_handle->execute("SELECT ss_applications.aid, a_name FROM ss_applications, u_applications WHERE
				ss_applications.aid = u_applications.aid AND u_applications.uid = " . $this->user->get_id());
			
			if ($valid_apps) {
				foreach ($valid_apps as $application) {
					$this->app_list_by_id[$application['aid']] = $application['a_name'];
					$this->app_list_by_name[$application['a_name']] = $application['aid'];
				}
			} else
				throw new Exception("single_signon::__construct($username, ...) - failed to query users applications.");
		} else
			throw new Exception("single_signon::__construct($username, ...) - login failed. Either supplied username or password is invalid.");					
	}	
	
	/**
	 * Check for a valid login. Logins will expire after a 
	 * configurable amount of time
	 * 
	 * @return boolean
	 */
	public function is_valid() {		
		$current_time = time();
		
		/* compare the login time and validate if login has expired */
		if ($this->valid_time != 0 && ($this->login_valid_till < $current_time)) {
			self::$errstr = "single_signon::is_valid() - login has expired ($this->login_valid_till < $current_time).";
			return false;
		}

		return true;
	}
	
	/**
	 * Check if user is allowed to use application
	 * 
	 * @param string $app_name
	 * @return boolean
	 */
	public function check_application($app_name) { return isset($this->app_list_by_name[$app_name]); }
	
	/**
	 * Return the current error string
	 * 
	 * @return string	 
	 */
	public function get_errstr() { return $this->errstr; }
}
?>
