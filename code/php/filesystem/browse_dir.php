<!DOCTYPE html
PUBLIC "-//W#C//DTD XHTML 1.0 Strict//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title>Documentation Listing</title>
	</head>
	<body bgcolor="grey">
	   <table border="0">
		   <tr>
			   <td>Documents</td>
		   </tr>
<?php
/*
   vim:ts=3:sw=3:

   Script used to generate links from files present in a directory

   $Author: cyrus $
   $Date: 2004/01/23 00:47:22 $
   $Revision: 1.1 $
*/

require_once '../common/functions.php';

setResponse();
$directory = "/home/cyrus/documentation/htdocs/" . $response['directory'];

// Open a known directory, and proceed to read its contents
if (is_dir($directory)) {
	if ($dir_handle = opendir($directory)) {
		while (($fh = readdir($dir_handle)) !== false) {
			if (strcmp($fh, ".") && strcmp($fh, "..")) {
				$link = $directory . "/" . $fh;
				if (!is_dir($directory . "/" . $fh))
					printf("<tr><td><a href=file://%s>%s</a></td></tr>\n", 
							$link, $fh);
				else
					printf("<tr><td><a href=file://%s/index.html>%s</a></td></tr>
							\n", $link, $fh);
			}
		}
	}
} else 
	print "Location specified is not a directory or no location specified\n";
?>
		</table>
	</body>
</html>
