<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
require_once 'rw_semaphore.php';

$rw_sem = new rw_semaphore();

$rw_sem->read_access();
print "got read access 1\n";
$rw_sem->read_release();
$rw_sem->read_access();
print "got read access 2\n";
$rw_sem->read_release();
$rw_sem->write_access();
print "got write access 1\n";
$rw_sem->write_release();
print "released write\n";
?>