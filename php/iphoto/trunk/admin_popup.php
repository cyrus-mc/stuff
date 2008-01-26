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

include('config.php');
include('includes/functions.php');

if(@$_COOKIE['ifotocp'] == md5($admin_username.$admin_password)){
	html_interface();
}else{
	echo 'Please login.';
	exit();
}

function popup_content(){

	$id_request = @$_GET['id'];

	define('ID_REQUEST', $id_request);



	$popup_case = @$_GET['pop'];

	switch($popup_case){
		case 'dir_masteradd' : 	dir_masteradd(); 	break;
		case 'dir_add' : 		dir_add(); 			break;
		case 'dir_genthumbs' : 	dir_genthumbs();	break;
		case 'dir_upload' : 	dir_upload(); 		break;
		case 'dir_rename' : 	dir_rename(); 		break;
		case 'dir_delete' : 	dir_delete(); 		break;
		case 'file_rename' : 	file_rename(); 		break;
		case 'file_desc' : 		file_desc(); 		break;
		case 'file_delete' : 	file_del(); 		break;
	}

}


function dir_masteradd(){

	if(post_param('submit') && post_param('album_name')){

		$dir_to_create = post_param('album_name');

		if($dir_to_create == ''){
			exit('Please type the gallery name.');
		}

		if(is_dir('./gallery/'.$dir_to_create)){
			exit('Gallery named '.$dir_to_create.' already existed. Click <a href="#" onClick="window.history.go(-1)">here</a> to try again.');
		}

		if(mkdir('./gallery/'.$dir_to_create)){
			echo 'Gallery created!';
		}else{
			echo 'Cannot create gallery. Please create it using FTP instead.';
		}



		?>

	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Add New Album</h1>
		<form action="?pop=dir_masteradd" method="POST">
			Album Name<br />
			<input type="text" name="album_name" /><br />
			<input type="submit" name="submit" value="Submit" /> <input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}
}

function dir_add(){

	if(post_param('submit') && post_param('album_name') && get_param('id')){

		$main_dir		= get_param('id');
		$dir_to_create	= post_param('album_name');

		if($dir_to_create == ''){
			exit('Please type the gallery name.');
		}
		if(is_dir('./gallery/'.$main_dir.'/'.$dir_to_create)){
			exit('Gallery named '.$dir_to_create.' already existed. Click <a href="#" onClick="window.history.go(-1)">here</a> to try again.');
		}

		if(mkdir('./gallery/'.$main_dir.'/'.$dir_to_create)){
			echo 'Sub-gallery created!';
		}else{
			echo 'Cannot create sub-gallery. Please create it using FTP instead.';
		}

		?>

	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Add New Album</h1>
		<form action="?pop=dir_add&id=<?=get_param('id')?>" method="POST">
			Album Name<br />
			<input type="text" name="album_name" /><br />
			<input type="submit" name="submit" value="Submit" /> <input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}


}

function dir_genthumbs(){

	if(post_param('submit') && get_param('id')){

		require('includes/thumbnailer.php');

		$dir_id = trim(get_param('id'));

		$files = directoryToArray('gallery/'.$dir_id, true);

		foreach($files as $file){

			if ($file != '.' AND $file != '..' AND (substr(strtolower($file),-3) == 'jpg' || substr(strtolower($file),-3) == 'gif' || substr(strtolower($file),-3) == 'png')) {

				$file_split_arr = explode('/', $file);

				$total_arr = count($file_split_arr);
				if($total_arr == 3){
					$current_dir = $file_split_arr[1];
					$filename_only = $file_split_arr[2];
				}elseif($total_arr == 4){
					$current_dir = $file_split_arr[1] . '/' . $file_split_arr[2];
					$filename_only = $file_split_arr[3];
				}

				if(!file_exists('data/thumbdata_'.base64_encode($current_dir.']|['.$filename_only.']|['.filectime($file)).'.jpg') AND (substr(strtolower($file), -3) == 'jpg' || substr(strtolower($file), -3) == 'gif')){
					$tis = new ThumbnailImage();
					$tis->src_file  = $file;
					$tis->dest_type = THUMB_JPEG;
					//$tis->dest_file = 'data/thumbdata_'.base64_encode($current_dir.']|['.$file . ']|[' . filemtime('gallery/'.$current_dir.'/'.$file)).'.jpg';
					$tis->dest_file = 'data/thumbdata_'.base64_encode($current_dir.']|['.$filename_only . ']|[' . filectime($file)).'.jpg';
					$tis->max_width = 120;
					$tis->max_height = 4000;
					$tis->Output();
				}

			}

		}

		?>
		Thumbnail generated successful!
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Regenerate Thumbnails</h1>
		<form action="?pop=dir_genthumbs&id=<?=get_param('id')?>" method="POST" name="generateThumb">
			Thumbnail generation, may took up to 30 seconds to 1 minute. If this window display a blank white page appear after 30 second, please press <b>F5</b> on your keyboard to resume thumbnail generation.<br /><br />
			<input type="submit" name="submit" value="Generate Thumbnails" onclick="this.value='Processing...';" />
			<input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}


}

function dir_upload(){

	if(post_param('submit')){

		$dir_target = get_param('id');

		if(!is_dir('./gallery/'.$dir_target)){
			exit('Gallery named '.$dir_target.' didn\'t exist. Click <a href="#" onClick="window.history.go(-1)">here</a> to try again.');
		}

        $uploaddir  = './gallery/'.$dir_target;

        $file_name = $_FILES['file_name']['name'];
        $true_name = str_replace(" ","_",$file_name);
        $file_name = str_replace("%20","",$true_name);
        $uploadfile = $uploaddir . '/' . $file_name;
        if (move_uploaded_file($_FILES['file_name']['tmp_name'], $uploadfile)) {

	        // chmoded to 777, so that the FTP user can update this file also
	        chmod('./gallery/'.$dir_target, 0777);


	        require('includes/thumbnailer.php');

			if(!file_exists('data/thumbdata_'.base64_encode($dir_target.']|['.$file_name.']|['.filectime('gallery/'.$dir_target.'/'.$file_name)).'.jpg') AND (substr(strtolower($file_name), -3) == 'jpg' || substr(strtolower($file_name), -3) == 'gif')){
				$tis = new ThumbnailImage();
				$tis->src_file  = 'gallery/'.$dir_target.'/'.$file_name;
				$tis->dest_type = THUMB_JPEG;
				//$tis->dest_file = 'data/thumbdata_'.base64_encode($current_dir.']|['.$file . ']|[' . filemtime('gallery/'.$current_dir.'/'.$file)).'.jpg';
				$tis->dest_file = 'data/thumbdata_'.base64_encode($dir_target.']|['.$file_name . ']|[' . filectime('gallery/'.$dir_target.'/'.$file_name)).'.jpg';
				$tis->max_width = 120;
				$tis->max_height = 4000;
				$tis->Output();
			}


        	echo 'File uploaded succcesfully. Click <a href="?pop=dir_upload&id='.get_param('id').'">here</a> to upload more file.';
        	//print_r($_FILES);


        } else {
            echo 'Upload Error.';
            //print_r($_FILES);
        }

		?>
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Upload New File</h1>
		<form action="?pop=dir_upload&id=<?=get_param('id')?>" method="POST" enctype="multipart/form-data">
			<input type="file" name="file_name" /><br />
			<input type="submit" name="submit" value="Submit" onclick="this.value='Uploading...';" />
			<input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		Note : To upload more file, please use FTP instead.
		<?

	}


}

function dir_rename(){

	if(post_param('submit') && post_param('album_name')){

		$old_name = get_param('id');
		$new_name = post_param('album_name');

		if($old_name == '' OR $new_name == ''){
			exit('Please type the gallery name.');
		}

		if(!is_dir('./gallery/'.$old_name)){
			exit($dir_to_create.' is not a directory. Click <a href="#" onClick="window.history.go(-1)">here</a> to try again.');
		}

		if(rename('./gallery/'.$old_name, './gallery/'.$new_name)){
			echo 'Gallery renamed! You need to re-generate the thumbnails.';
		}else{
			echo 'Cannot rename gallery. Please rename it using FTP instead.';
		}

		?>
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Rename Album</h1>
		<form action="?pop=dir_rename&id=<?=get_param('id')?>" method="POST">
			Album Name<br />
			<input type="text" name="album_name" value="<?=get_param('id')?>" style="width:300px;" /><br />
			<input type="submit" name="submit" value="Submit" /> <input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}

}

function dir_delete(){

	if(post_param('submit') && get_param('id')){

		$dir_to_create = get_param('id');

		if($dir_to_create == ''){
			exit('Please type the gallery name.');
		}
		if(is_dir('./gallery/'.$dir_to_create)){

			if(RemoveDirectory('./gallery/'.$dir_to_create)){
			   echo 'Gallery deleted!';
			}else{
			   echo 'Cannot delete gallery directory. Please remove it using FTP instead.';
			}

		}



		?>

	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Delete Album</h1>
		<form action="?pop=dir_delete&id=<?=get_param('id')?>" method="POST">
			Are you sure to delete ? All photos under this album will be delete too.<br />
			<input type="submit" name="submit" value="Submit" /> <input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}


}

function file_rename(){


	if(post_param('submit') && get_param('id') && post_param('file_name')){

		$file_to_rename = base64_decode(get_param('id'));
		$file_to_rename_arr = explode(']|[', $file_to_rename);

		$dir = $file_to_rename_arr[0];
		//echo ' - ';
		$old_file_name = $file_to_rename_arr[1];
		//echo ' - ';

		$get_file_ext = substr($old_file_name, -4);
		$new_file_name = str_replace(' ','_',post_param('file_name')) . $get_file_ext;
		//echo ' - ';

		unlink('data/thumbdata_'.get_param('id').'.jpg');

		if(@rename('./gallery/'.$dir . '/' . $old_file_name, './gallery/'.$dir . '/' . $new_file_name)){
			echo 'Photo filename renamed.';

	        require('includes/thumbnailer.php');

			if(!file_exists('data/thumbdata_'.base64_encode($dir.']|['.$new_file_name.']|['.filectime('gallery/'.$dir.'/'.$new_file_name)).'.jpg') AND (substr(strtolower($new_file_name), -3) == 'jpg' || substr(strtolower($new_file_name), -3) == 'gif')){
				$tis = new ThumbnailImage();
				$tis->src_file  = 'gallery/'.$dir.'/'.$new_file_name;
				$tis->dest_type = THUMB_JPEG;
				//$tis->dest_file = 'data/thumbdata_'.base64_encode($current_dir.']|['.$file . ']|[' . filemtime('gallery/'.$current_dir.'/'.$file)).'.jpg';
				$tis->dest_file = 'data/thumbdata_'.base64_encode($dir.']|['.$new_file_name . ']|[' . filectime('gallery/'.$dir.'/'.$new_file_name)).'.jpg';
				$tis->max_width = 120;
				$tis->max_height = 4000;
				$tis->Output();
			}

		}else{
			echo 'Cannot rename this photo. If it uploaded using FTP, please rename it using FTP program.';
		}

		?>
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		$file_id = base64_decode(get_param('id'));
		$file_meta_arr = explode(']|[', $file_id);
		$file_name = str_replace('_',' ',substr($file_meta_arr[1], 0, -4));

		?>
		<h1>Photo Rename</h1>
		<form action="?pop=file_rename&id=<?=get_param('id')?>" method="POST">
			Photo Name<br />
			<input type="text" name="file_name" value="<?=$file_name?>" style="width:300px;" /><br />
			<input type="submit" name="submit" value="Submit" /> <input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}

}

function file_desc(){

	if(post_param('submit') && get_param('id') && post_param('file_desc')){

		$file_id = get_param('id');

		$content = post_param('file_desc');

		$fp = fopen('data/desc_'.$file_id.'.desc', 'w');
		fwrite($fp, $content);
		fclose($fp);

		echo 'Description updated.';

		?>
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		if(file_exists('data/desc_'.get_param('id').'.desc')){
			$file_description = implode ('', file ('data/desc_'.get_param('id').'.desc'));
		}else{
			$file_description = '';
		}

		?>
		<h1>Photo Description</h1>
		<form action="?pop=file_desc&id=<?=get_param('id')?>" method="POST">
			Description<br />
			<input type="text" name="file_desc" value="<?=$file_description?>" style="width:400px;" /><br />
			<input type="submit" name="submit" value="Submit" />
			<input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}
}

function file_del(){

	if(post_param('submit') && get_param('id')){

		$file_to_delete = get_param('id');

		$file_to_delete_arr = explode(']|[', base64_decode($file_to_delete));

		if(@unlink('./gallery/'.$file_to_delete_arr[0].'/'.$file_to_delete_arr[1])){
			echo 'Photo deleted.';
		}else{
			echo 'Cannot delete this photo. If it uploaded using FTP, please delete it using FTP program.';
		}

		?>
	    <br /><br /><a href="javascript:tutupWindow();">Close</a>
	    <script language="JavaScript">
	        <!--
	        function tutupWindow() {
	        opener.location.reload();
	        self.close();
	        }
	        // -->
	    </script>

		<?

	}else{

		?>
		<h1>Delete Photo</h1>
		<form action="?pop=file_delete&id=<?=get_param('id')?>" method="POST">
			Are you sure to delete this photo?<br />
			<input type="submit" name="submit" value="Submit" />
			<input type="button" name="cancel" value="Cancel" onclick="window.close();" />
		</form>
		<?

	}
}


function html_interface(){

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>iFoto - My Simple Photo Gallery</title>
	<style type="text/css">
	<!--
	html, body {font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #494949; background-color:#DDDDDD; text-align:center;}
	h1 { font-size:12px; padding:5px; border:1px solid #CCCCCC; background:#E2E2E2;}
	a:link {color: #006699; text-decoration: none;}
	a:visited {text-decoration: none; color: #006699;}
	a:hover {text-decoration: underline; color: #000000;}
	a:active {text-decoration: none; color: #006699;}
	-->
	</style>
	</head>
	<body>
		<?php popup_content(); ?>
	</body>
	</html>
	<?

}

?>