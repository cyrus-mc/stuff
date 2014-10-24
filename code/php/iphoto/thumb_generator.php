<?php

require('includes/functions.php');
require('includes/thumbnailer.php');
require_once('config.php');

function __autoload($class_name) {
	require_once 'iphoto/plugins/' . $class_name . '.php';
}

session_start();

$response = "";
if ( count($HTTP_POST_VARS) > 0 )
	$response = $HTTP_POST_VARS;
else
	$response = $HTTP_GET_VARS;

$current_dir = $response['dir'];
$opendir = __GALLERY_DIR__ . '/' .$current_dir;
if ($current_dir != '{TDIR}') {
	/* loop over image items and create thumbnails as needed */
	$object_list = $_SESSION[md5($current_dir)]['object_list'];
	foreach ($object_list as $key => $object) {
		$details = $object->return_as_array();
		if ($details['item_type'] == 'image' AND !$object->get_has_thumb() AND !file_exists('data/' . $details['thumbnail'])) {
			$new_filename = 'data/thumbdata_' . base64_encode($current_dir . ']|[' . $details['filename'] . ']|[' . $details['last_modified']) . '.jpg';
			$tis = new ThumbnailImage();
			$tis->src_file = $opendir . $details['filename'];
			$tis->dest_type = THUMB_JPEG;
			$tis->dest_file = $new_filename;
			$tis->max_width = 120;
			$tis->max_height = 4000;
			$tis->Output();
			/* update object */
			$object->set_thumbnail($new_filename);
			$object->set_has_thumb(true);
		}
	}
}
?>
