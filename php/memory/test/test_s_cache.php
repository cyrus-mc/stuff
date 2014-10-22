<?php

error_reporting (E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

/* include the required files */
require_once 's_cache.php';

/* simple test suite for Size cache (s_cache) */
$cache = new s_cache(5);

/* add some cache lines */
$cache->add('t_key_1', 'cache_line_1');
$cache->add('t_key_2', 'cache_line_2');
$cache->add('t_key_3', 'cache_line_3');
$cache->add('t_key_4', 'cache_line_4');
$cache->add('t_key_5', 'cache_line_5');
/* print out the cache */
$cache->print_cache();
if (($cache_line = $cache->get('t_key_1')))
	print "\nfound cache line - t_key_1 - with value ". $cache_line['contents'] . "\n\n";
	
/* add another element, which exceeds size, oldest element removed */
$cache->add('t_key_6', 'cache_line_6');
/* print out the cache */
$cache->print_cache();
	
	//if ($t_cache->set_m_dirty(array('test1', 'test5'))) {
//		print "set all to dirty\n";
//	} else {
		//$t_cache->print_err();
	//}	
?>