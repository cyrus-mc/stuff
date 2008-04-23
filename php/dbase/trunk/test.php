<?php

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
//print microtime() . "\n";
$dbh->execute("select uname from accounts");
//print microtime() . "\n";
$dbh->execute("select comment from accounts");
$dbh->print_cache();
$dbh->print_atimes();
$dbh->print_key_to_table_cache();
$dbh->print_table_to_key_cache();
print "\n\nafter\n\n";
$dbh->execute("select pword from accounts");
//print md5("select pword from accounts") . "\n";
$dbh->print_cache();
$dbh->print_atimes();
$dbh->print_key_to_table_cache();
$dbh->print_table_to_key_cache();
//print microtime() . "\n";
//$dbh->print_key_to_table_cache();
//$dbh->print_table_to_key_cache();
//$dbh->print_atimes();
//$dbh->execute("select uname from accounts");
//$dbh->print_atimes();
//$dbh->execute("insert into accounts values('test', 'test_pass', 6, 'test_comment')");
//$dbh->execute("update accounts set uname = 'test2' where uname= 'test'");
//$dbh->execute("delete from accounts where uname = 'test'");
$dbh->print_err();
$dbh->disconnect();
?>