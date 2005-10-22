<?php
/*
   vim:ts=3 sw=3:

   Test platform for UserBean and Authentication classes

   $Author: cyrus $
   $Date: 2004/01/22 20:45:10 $
   $Revision: 1.1 $
*/

// included classes
require_once 'authentication.php';

$user_info = array();
$user_info['username'] = "Matthew";
$user_info['userid'] = 10;

$ub = new UserBean($user_info);

$ub->setField('new_field', 'new_value');
$ub->printThis();

if ($ub->removeField('ew_field')) {
	$ub->printThis();
} else {
	print "Field not present\n";
}

$auth = new Authenticate(10, $ub->getField('userid'));
print $auth->getAuthorized();
print "\n";

?>
