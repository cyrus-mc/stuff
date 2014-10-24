<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/* include the required files */
require_once 'single_signon.php';
require_once 'application_container.php';
require_once 'dbase/db_postgres.php';

try {
	$app_container = new application_container('sample_php_application');
	/* check if environment existed before */
	if ($app_container->get_init_flag())
		print "Environment already existed, restoring\n";		
	else
		print "Environment not stored, initializing\n";

	$db_connection = null;
	if (!($db_connection = $app_container->get_object('db_handle')))
		$db_connection = new db_postgres("cyrus:x8bkelpa@cyrus.localhost:5432");		
		
	/* connect to database */
	//if ($db_connection->connect()) {
	$sign_on = new single_signon('t_user', 'test_password', $db_connection, 0);
	//}
	
	if ($sign_on->check_application('test_app'))
		print "allowed\n";
		
	print "Clean cache hits = " . $db_connection->get_clean_cache_hits() . ", Dirty cache hits = " . $db_connection->get_dirty_cache_hits() . ", Cache misses = " . $db_connection->get_cache_misses() . "\n";
	$db_connection->disconnect();
	
	$app_container->add_object('db_handle', $db_connection, true);
} catch (Exception $e) {
	print $db_connection->get_errstr() . "\n";
	$db_connection->disconnect();
	print "caught exception $e\n";
}
?>