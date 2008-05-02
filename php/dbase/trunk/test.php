<?php
//error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once 'db_postgres.php';

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

$dbh = new db_postgres("cyrus:x8bkelpa@cyrus.localhost:5432");

if ($dbh->connect()) {
	print "connected\n";
} else {
	$dbh->print_err();
}

$dbh->execute("select comment from accounts");
$dbh->execute("select pword from accounts");
//$dbh->print_cache();
//$dbh->print_key_to_table_cache();
//$dbh->print_table_to_key_cache();
$dbh->execute("select uname from accounts");
//$dbh->print_cache();
//$dbh->print_key_to_table_cache();
//$dbh->print_table_to_key_cache();
$dbh->execute("select pword from accounts");
$dbh->execute("select uname from accounts");
$dbh->execute("select comment from accounts");
$dbh->execute("select uname from accounts");
$dbh->execute("select pword from accounts");
$dbh->execute("select comment from accounts");
$dbh->execute("select pword from accounts");
$dbh->execute("select uname from accounts");
$dbh->execute("select comment from accounts");
//var_dump(pg_fetch_all($result));
//$dbh->execute("insert into accounts values('test', 'test_pass', 6, 'test_comment')");
//$dbh->execute("delete from accounts where uname='test'");$dbh->execute("select pword from accounts");
//var_dump(pg_fetch_all($result2));
$dbh->print_cache();
$dbh->print_key_to_table_cache();
$dbh->print_table_to_key_cache();
print "Clean cache hits = " . $dbh->get_clean_cache_hits() . ", Dirty cache hits = " . $dbh->get_dirty_cache_hits() . ", Cache misses = " . $dbh->get_cache_misses() . "\n";
$dbh->disconnect();
?>
