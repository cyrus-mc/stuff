<?php
error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/* include the required files */
require_once 'rw_fifo.php';

/* simple test suite for Read/Writer semaphore */
try {
	/* create a reader/writer */
	$reader_writer = new rw_fifo('reader_writer');	
	
	/* request read access */
	$reader_writer->write_request();
	print "** obtained exclusive write access **\n";

	/* sleep for 10 seconds */
	sleep(10);
	
	/* release current access */
	$reader_writer->write_release();
	print "** released exclusive write access **\n";	
} catch (Exception $e) {
	print "caught exception $e\n";
}
?>