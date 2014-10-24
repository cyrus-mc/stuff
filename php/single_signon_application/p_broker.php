<?php

require_once 'application/session.php';
require_once 'application/application_container.php';
require_once 'application/single_signon.php';
require_once 'dbase/db_postgres.php';

$application_container = null;
$db_postgres = null;
$single_signon = null;
$session = null;

function initiate_error($session, $errstr) {
	$session->session_register('error_string', $errstr);
	header("Location: error.html");	
}

try {
	/* initialize the session */
	$session = new session();
	
	$application_container = new application_container('single_signon');	
	
	/* create database object if needed (lock) */
	$application_container->lock_write();
	if (!$application_container->get_init_flag())
		if (!$application_container->add_object('db_handle', new db_postgres("cyrus:x8bkelpa@cyrus.localhost:5432")))			
			throw new Exception($application_container->get_errstr());
	
	/* now get the database object */	
	if ( !($db_postgres = $application_container->get_object('db_handle')) )
		throw new Exception($application_container->get_errstr());
			
	$application_container->write_release();
		
	/* check if user is already validated */
	if ( !($single_signon = $session->session_get('single_signon')) )
		$single_signon = new single_signon($session->query_get('u_name', 0), $session->query_get('p_word', 0), $db_postgres, 0);

	$application_container->lock_write();
	/* we are done, so write db_handle object back to application container */
	if ( !($application_container->set_object('db_handle', $db_postgres)) )		
		throw new Exception($application_container->get_errstr());
	
	$application_container->write_release();
	$session->session_register('single_signon', $single_signon, true);
		
	/* login or deny */	
	if ($single_signon->is_valid() && $single_signon->check_application('test_app'))
		header("Location: success.html?a_name=test_app");			
	else
		header("Location: access_denied.html");
	
} catch (Exception $e) {
	/* release our hold on the application container */
	$application_container->write_release();
	
	/* if an exception is caught, clean up */	
	if ($db_postgres)
		$db_postgres->disconnect();
		
	/* release any objects we may have a hold of */
	header("Location: error.html");	
}
?>