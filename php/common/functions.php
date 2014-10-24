<?php
/*
   vim:ts=3:sw=3:

   Common PHP functionality implemented in this file

   $Author: cyrus $
   $Date: 2004/01/23 23:37:46 $
   $Revision: 1.2 $
*/

$response = "";
/* set the response variable so we can access post/get fields through a
   common interface */
if ( count($HTTP_POST_VARS) > 0 )
	$response = $HTTP_POST_VARS;
else 
	$response = $HTTP_GET_VARS;

/* set base URL for requests - takes into consideration user directories */
$baseURL = "";
if ($_SERVER['REQUEST_URI'][1] == "~") {
	$baseURL = "/" . substr($_SERVER['REQUEST_URI'], 1, strpos($_SERVER['REQUEST_URI'], '/', 1) - 1);
}
?>
