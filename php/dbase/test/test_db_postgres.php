<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

require_once 'db_postgres.php';

function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

/* simple test suite for Database classes */
try {
	$db_connection = new db_postgres("cyrus:x8bkelpa@cyrus.localhost:5432");

	/* connect to database */
	if ($db_connection->connect()) {
		/* perform some queries */
		$db_connection->execute("select comment from accounts", db_postgres::USER_CACHE_LINE);
		$db_connection->execute("select pword from accounts");
		$db_connection->execute("insert into accounts values('test', 'test_pass', 6, 'test_comment')", db_postgres::USER_CACHE_LINE);

		$db_connection->execute("delete from accounts where uname='test'");
		$db_connection->execute("select pword from accounts");
		/* print the cache tables */
		$db_connection->print_cache();
		$db_connection->print_key_to_table_cache();
		$db_connection->print_table_to_key_cache();
		/* print the cache statistics */
		print "Clean cache hits = " . $db_connection->get_clean_cache_hits() . ", Dirty cache hits = " . $db_connection->get_dirty_cache_hits() . ", Cache misses = " . $db_connection->get_cache_misses() . "\n";
		$db_connection->disconnect();		
	} else
		$db_connection->print_err();
} catch (Exception $e) {
	print "caught exception $e\n";
}

?>
