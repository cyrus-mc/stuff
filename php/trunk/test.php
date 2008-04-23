<?php

	require_once 's_cache.php';

/*	$t_cache = new s_cache(10);
	$t_cache->add('test1', 'testing_1');
	$t_cache->add('test2', 'testing_2');
	$t_cache->add('test3', 'testing_3');
	$t_cache->add('test4', 'testing_4');
	$t_cache->add('test5', 'testing_5');
	$t_cache->add('test6', 'testing_6');
	$t_cache->add('test7', 'testing_7');
	$t_cache->add('test8', 'testing_8');
	$t_cache->add('test9', 'testing_9');
	$t_cache->add('test10', 'testing_10');
	$t_cache->add('test11', 'testing_11');
	$t_cache->get('test2');
	$t_cache->set('test3', 'testing_update_3');
	$t_cache->add('test12', 'testing_12');
	$t_cache->print_cache();
	$t_cache->print_atimes();
	*/
	$sarray = array('43jkda34' => 'value1', 4 => 'value2', '5325325f' => 'value3');	
	print_r(array_values($sarray));
	//$t_cache->get('test3');
	//$t_cache->get('test1');
	//$t_cache->add('test11', 'testing_11');	
	//$t_cache->get('test6');
	//$t_cache->print_atimes();	
?>