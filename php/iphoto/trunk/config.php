<?php

$sort_thumbs_by 	    = 'filename'; 	//sort the thumbs by using one of these values - 'filename', 'title', 'width', 'height', 'filesize', 'modified'
$sort_thumbs_order 	    = 'asc';
$strip_sort_number 	    = true; 		//this will stip 1_ , 2_ , 3_ ...etc in beginning of the filename in thumbnails title.
$display_home_last_addition = true; 		//true will display last added photos at homepage

$thumbs_per_page 	    = 10; 			// default is 0, unlimited thumbs per page.

$tile_style		    = 'table';		// table or css


$admin_username		    = 'admin';
$admin_password		    = 'mypassword';

/* new configuration parameters */
define('__SORT_BY__', 'filename');
define('__SORT_ORDER__', 'asc');
define('__STRIP_SORT_NUMBER__', true);
define('__DISPLAY_HOMEPAGE_LAST_ADDITION__', true);
define('__THUMBS_PER_PAGE__', 10);
define('__TILE_STYLE__', 'table');
define('__MENU_SPACER__', '');
define('__MAX_WIDTH__', 560);

define('__GALLERY_DIR__', 'gallery');
define('__DIRECTORY_THUMBNAIL__', 'directory.jpg');
define('__AUDIO_THUMBNAIL__', 'mp3.png');
define('__TEMPLATE__', 'default.tpl');

/* define the file types and classes that are handled */
$file_types = array('jpg' => 'image',
		'jpeg' => 'image',
		'png' => 'image',
		'gif' => 'image',
		'mp3' => 'mp3');
?>
