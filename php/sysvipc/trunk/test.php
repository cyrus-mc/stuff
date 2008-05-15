<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/* include the required files */
require_once 'rw_flock.php';

/* simple test suite for Read/Writer semaphore */
try {
	/* create a reader/writer */
	$reader_writer = new rw_flock('reader_writer');	
	
	/* request read access */
	$reader_writer->read();
	print "** obtained shared read access **\n";

	/* release current access */
	$reader_writer->release();
	print "** released shared read access **\n";
	
	/* request write access */
	$reader_writer->write();
	print "** obtained exclusive write access **\n";
	
	/* release current access */
	$reader_writer->release();
	print "** released exclusive write access **\n";	
} catch (Exception $e) {
	print "caught exception $e\n";
}
?>