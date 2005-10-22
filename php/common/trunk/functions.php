<?php
/*
   vim:ts=3:sw=3:

   Common php functions are defined and implemented in this file

   $Author: cyrus $
   $Date: 2004/01/23 23:37:46 $
   $Revision: 1.2 $
*/

// variable used to point to form response type
$response = "";

/*
	Function use to set response variable
*/
function setResponse() {
	global $HTTP_POST_VARS, $HTTP_GET_VARS, $response;

	if ( count($HTTP_POST_VARS) > 0 )
		$response = $HTTP_POST_VARS;
	else 
		$response = $HTTP_GET_VARS;
}
?>
