<?php

	require_once 'user.php';
	require_once 'permission.php';

	$groups = array('1' => 'group1', '2' => 'group2');
	$mgroup = "test";
	$user_object = new User('matthew', 101, $groups);			
		
	$permission_object = new Permission('test_page_1', new User('matthew', 101, array('1' => 'group1')), 731, true);
	
	//print_r($permission_object);
	//if ($permission_object->group_has_read()) {
	//	print "Permission is granted\n";
	//} else {
	//	print "Permission is denied\n";
	//}
	
	$tarray = array('1' => 'test', '2' => 'file');
	print_r($tarray);
	unset($tarray);
	print_r($tarray);
?>