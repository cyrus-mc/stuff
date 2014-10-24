<?php
/***********************************************************************

  Copyright (C) 2006 Aizu Ikmal Ahmad (aizu@ikmal.com.my)

  This file is part of iFoto.

  iFoto is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  iFoto is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/

require('config.php');
require('includes/functions.php');
require('includes/thumbnailer.php');
require_once('includes/FastTemplate.php');

function __autoload($class_name) {
	require_once 'plugins/' . $class_name . '.php';
}

/* define functions */
function errorMsg($error_text) {
	echo '<b><font color="red">ERROR : </font> ' . $error_text . '</b>';
	exit();
}

function readDirectory($base_dir, $current_dir = '', $exclude_dir = false) {
	global $file_types;
	$object_list = array();
	$cobject = null;
	$prev = null;


	if ($handle = opendir($base_dir . $current_dir)) {
		while (false !== ($file = readdir($handle))) {
			list($extension) = array_reverse(explode(".", $file));
			if ($file != '.' AND $file != '..') {
				/* if I put a reference here will it still work */
				$prev = $cobject;
				if (is_dir($base_dir . $current_dir . $file) AND !$exclude_dir)
					$cobject = new dir($base_dir, $current_dir, $file, $extension);
				elseif (isset($file_types[$extension]))
					$cobject = new $file_types[$extension]($base_dir, $current_dir, $file, $extension);
			
				$cobject->set_prev($prev);
				if ($prev != null)
					$prev->set_next($cobject);
				$object_list[$cobject->get_id()] = $cobject;
			}
		}
		/* set the prev and next for first and last item*/

		$fobject = current($object_list);
		if ($fobject != null) {
			$fobject->set_prev($cobject);	
			$cobject->set_next($fobject);
		}
		/* done */
		closedir($handle);
		return $object_list;
	} else
		return false;
}

function map_gallery_directories($current_dir, $spacer) {
        global $response, $tpl;

	if (is_dir($current_dir)) {
		foreach(glob($current_dir . "/*", GLOB_ONLYDIR) as $directory) {
			$tpl->assign( array('{DIRECTORY}' => substr($directory, strpos($directory, '/') + 1, strlen($directory)), '{MENU_OPTION}' => $spacer . basename($directory)) );
			$tpl->parse(MENU, '.menu');
			map_gallery_directories($directory, __MENU_SPACER__);
		}
	}
}

/* start the session */
session_start();

requirement_checker();

if (!defined('__GALLERY_DIR__'))
	define('__GALLERY_DIR__', '');

$tpl = new FastTemplate('templates');
$tpl->define( array(body => 'default.tpl',
		row => 'image-rows.tpl',
		menu => 'menu-rows.tpl',
		view => 'image-view.tpl') );

/* set the response variable so we can access post/get fields through a common interface */
$response = "";
if ( count($HTTP_POST_VARS) > 0 ) 
	$response = $HTTP_POST_VARS;
else
	$response = $HTTP_GET_VARS;


$column = 0;
$action = $response['action'];

/* parse the directory parameter (needed for all actions */
$dir = $response['dir']; 
if ($action != '' AND $dir == '')
	errorMsg('supplied post/get variable dir is emtpy');

if (substr($dir, strlen($dir) - 1, 1) != '/')
	$dir = $dir . '/';
if (substr($dir, 0, 1) == '/')
	$dir = substr($dir, 1, strlen($dir));
$dir_id = md5($dir);
/* done parsing of dir parameter */

/* generate naviation menu */
map_gallery_directories(__GALLERY_DIR__, '');

if ($action == 'list') {
	$object_list = null;
	if ( ($dobject = $_SESSION[$dir_id]) ) {
		/* check the timestamp on that directory to see if we need to update the object */
		if ($_SESSION[$dir_id]['last_modified'] != filectime(__GALLERY_DIR__ . '/' . $dir)) {
			$_SESSION[$dir_id]['last_modified'] = filectime(__GALLERY_DIR__ . '/' . $dir);
			$_SESSION[$dir_id]['object_list'] = readDirectory(__GALLERY_DIR__ . '/', $dir);
		}
		$object_list = $_SESSION[$dir_id]['object_list'];
	} else {
		$object_list = readDirectory(__GALLERY_DIR__ . '/', $dir);
		$_SESSION[$dir_id] = array ('last_modified' => filectime(__GALLERY_DIR__ . '/' . $dir), 'object_list' => $object_list);
	}

	/* loop over objects and parse template */
	if ($object_list) {
		foreach ($object_list as $key => $object) {
			$details = $object->return_as_array();
			$tpl->assign( array( '{IMAGE_' . $column . '}' => $object->draw(),
					'{TITLE_' . $column . '}' => substr(basename($details['filename'], '.' . $object->get_extension()), 0, 20)) );
			
			if ($column == 3)
				$tpl->parse(MAIN, ".row");
			$column = ($column + 1) % 4;
		}
		/* run parse one more time if needed to pick up the last 3 images */
		if ($column != 0) {
			$tpl->parse(MAIN, ".row");
		}
	}
	/* done looping objects */
	$tpl->assign( array( '{TDIR}' => urlencode($dir)));
} elseif ($action == 'view') {
	if (($object_id = $response['object']) == "")
		errorMsg('supplied post/get/variable object is empty');
	
	$object = $_SESSION[$dir_id]['object_list'][$object_id];
	$details = $object->return_as_array();
	$tpl->assign( array( '{IMAGE}' => __GALLERY_DIR__ . '/' . $details['directory'] . '/' . $details['filename'],
			'{WIDTH}' => $details['width'] > __MAX_WIDTH__ ? __MAX_WIDTH__ : $details['width'],
			'{ITITLE}' => substr(basename($details['filename'], '.' . $object->get_extension()), 0, 20),
			'{FILENAME}' => $details['filename'], '{ISIZE}' => $details['width'] . 'x' . $details['height'],
			'{MODIFIED}' => date('l, j F o', $details['last_modified']), '{FSIZE}' => number_format($details['filesize'] / 1024, 2, '.', '') . 'kb',
			'{PIMAGE}' => $object->get_prev()->draw(),
			'{NIMAGE}' => $object->get_next()->draw()) );

	$tpl->parse(MAIN, "view");
} else {
	$tpl->assign( array( 'MAIN' => '<tr><td><center><b>** Please select a gallery from the menu on the left **</b></center></td></tr>' ));
}
$tpl->parse(FINALPAGE, array('body'));
$tpl->FastPrint();

?>
