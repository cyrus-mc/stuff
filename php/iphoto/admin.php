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

if(@$_COOKIE['ifotocp'] == md5($admin_username.$admin_password)){
	if(@$_GET['p'] == 'logout'){
		logout();
	}else{
		html_interface();
	}
}else{
	if(@$_POST['login'] == $admin_username && @$_POST['password'] == $admin_password){

		setcookie('ifotocp', md5($admin_username.$admin_password), time()+3600, '/', false);

		//reload
		echo '<meta http-equiv=refresh content=0;URL=?>';
	}else{
		html_login();
	}
}


function admin_contents(){

	$page_request = @$_GET['p'];

	switch($page_request){
		case 'gallery' : gallery(); break;
		case 'comments' : comments(); break;
		case 'thumbnails' : thumbnails(); break;
		case 'sentinal' : sentinal(); break;
		default : homepage(); break;
	}

}


function homepage(){

	?>
	<center>
	<br /><br />
	Welcome to <b><font color="#FF6600">i</font>Foto Control Panel</b> version 1.0
	<br /><br />
	<b>NOTES</b><br />
	This is the initial release of control panel since a lot of user want to upload thier photos by using web.
	<br />
	But to enable this feature, you must make sure the 'gallery' directory is writable by webserver.
	<br /><br />
	If you have any questions or comments, feel free to contact me at <a href="mailto:aizu@ikmal.com.my">aizu@ikmal.com.my</a> (email).
	<br /><br />
	</center>
	<?

}

function gallery(){

	?>
	<div style="float:left; width:300px;">
		<b>Gallery List</b><br />
	</div>
	<div style="float:right; width:300px; text-align:right; padding-right:10px;">
		<a href="#" onclick="pop('dir_masteradd','')">Add New Album</a>
	</div>
	<br /><br />
	<?

	$opendiryg = "gallery/";
	if ($handle = opendir($opendiryg)) {
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != ".." && $file != "Thumbs.db") {
				?>
					<div style="border:1px solid #CCCCCC; background:#CCCCCC; padding:5px;">
						<div style="float:left; width:300px;">
							<b><?=$file?></b>
						</div>
						<div style="float:right; text-align:right; width:400px;">
							<?if(is_writable($opendiryg.$file)){?>
								<a href="#" onclick="pop('dir_add','<?=urlencode($file)?>')">Add new sub-gallery</a> | <a href="#" onclick="pop('dir_genthumbs','<?=urlencode($file)?>')">Regenerate Thumbs</a> | <a href="#" onclick="pop('dir_upload','<?=urlencode($file)?>')">Upload new photos</a> | <a href="#" onclick="pop('dir_rename','<?=urlencode($file)?>')">Rename</a> | <a href="#" onclick="pop('dir_delete','<?=urlencode($file)?>')">Delete</a>
							<?}else{?>
								<div style="border:1px solid #FC9A9A; background:#FEDADA; padding:0px; width:200px; text-align:center;">Directory not writable (<a href="">?</a>)</div>
							<?}?>
						</div>
						<div style="clear:both;"></div>
					</div>
				<?

				$opendiryg_sub = "gallery/$file";
				if ($handle_sub = opendir($opendiryg_sub)) {
					while (false !== ($file_sub = readdir($handle_sub))) {
						if ($file_sub != "." && $file_sub != ".." && $file_sub != "Thumbs.db") {
							if(is_dir($opendiryg_sub.'/'.$file_sub)){
								?>
									<div style="margin-left:20px; border:1px solid #CCCCCC; background:#E3E3E3; padding:5px;">
										<div style="float:left; width:360px;">
											<b><?=$file_sub?></b>
										</div>
										<div style="float:right; text-align:right; width:300px;">
											<?if(is_writable($opendiryg.$file)){?>
												<a href="#" onclick="pop('dir_genthumbs','<?=urlencode($file.'/'.$file_sub)?>')">Regenerate Thumbs</a> | <a href="#" onclick="pop('dir_upload','<?=urlencode($file.'/'.$file_sub)?>')">Upload new photos</a> | <a href="#" onclick="pop('dir_rename','<?=urlencode($file.'/'.$file_sub)?>')">Rename</a> | <a href="#" onclick="pop('dir_delete','<?=urlencode($file.'/'.$file_sub)?>')">Delete</a>
											<?}else{?>
												<div style="border:1px solid #FC9A9A; background:#FEDADA; padding:0px; width:200px; text-align:center;">Directory not writable (<a href="">?</a>)</div>
											<?}?>
										</div>
										<div style="clear:both;"></div>
									</div>
								<?

									$opendiryg_file = $opendiryg_sub.'/'.$file_sub;
									if ($handle_file = opendir($opendiryg_file)) {
										while (false !== ($file_tri = readdir($handle_file))) {
											if ($file_tri != "." && $file_tri != ".." && $file_tri != "Thumbs.db") {

												$file_id = base64_encode($file .'/' . $file_sub.']|['.$file_tri . ']|[' . filectime('gallery/'.$file .'/' . $file_sub.'/'.$file_tri));
												?>
													<div style="margin-left:40px; border:1px solid #CCCCCC; border-bottom:0px; padding:5px;">
														<img style="border:1px solid #666666;" src="data/thumbdata_<? echo base64_encode($file .'/' . $file_sub.']|['.$file_tri . ']|[' . filectime('gallery/'.$file .'/' . $file_sub.'/'.$file_tri)); ?>.jpg" width="20">
														<?=$file_tri?> -
														<?if(is_writable($opendiryg.$file)){?>
															<a href="#" onclick="pop('file_rename','<?=$file_id?>')">Rename Filename</a> | <a href="#" onclick="pop('file_desc','<?=$file_id?>')">Edit Description</a> | <a href="#" onclick="pop('file_delete','<?=$file_id?>')">Delete</a>
														<?}else{?>
															<span style="border:1px solid #FC9A9A; background:#FEDADA; padding:0px 10px; text-align:center;">File not writable (<a href="">?</a>)</span>
														<?}?>
													</div>
												<?
											}
										}
										closedir($handle_file);
									}



							}else{

								$file_id = base64_encode($file . ']|['.$file_sub . ']|[' . filectime('gallery/'.$file .'/' . $file_sub));
								?>
									<div style="margin-left:20px; border:1px solid #CCCCCC; border-bottom:0px; padding:5px;">
										<img style="border:1px solid #666666;" src="data/thumbdata_<? echo base64_encode($file.']|['.$file_sub . ']|[' . filectime('gallery/'.$file.'/'.$file_sub)); ?>.jpg" width="20">
										<?=$file_sub?> -
										<?if(is_writable($opendiryg.$file)){?>
											<a href="#" onclick="pop('file_rename','<?=$file_id?>')">Rename Filename</a> | <a href="#" onclick="pop('file_desc','<?=$file_id?>')">Edit Description</a> | <a href="#" onclick="pop('file_delete','<?=$file_id?>')">Delete</a>
										<?}else{?>
											<span style="border:1px solid #FC9A9A; background:#FEDADA; padding:0px 10px; text-align:center;">File not writable (<a href="">?</a>)</span>
										<?}?>
									</div>
								<?
							}

						}
					}
					closedir($handle_sub);
				}

			}
		}
		closedir($handle);
	}




}

function comments(){

	?>
	<b>Comment List</b>
	<div class="comments">
		<div class="list">
		<?

		$filename = 'data/comments.dat';

		if(!file_exists($filename)){
			$fp = fopen($filename, 'w');
			fwrite($fp, '');
			fclose($fp);
		}

		$comments_data = implode ('', file ($filename));

		$comments_arr = explode('}|+|{', $comments_data);

		if(@$_GET['act'] == 'delete'){

			$key_to_delete = $_GET['id'];

			unset($comments_arr[$key_to_delete]);

			$content_to_write = '';
			$total_comments = count($comments_arr);
			$i = 1;
			foreach ($comments_arr as $comments_data){
				if($i != $total_comments){$append_saperator = '}|+|{';}else{$append_saperator = '';}
				$content_to_write .= $comments_data.$append_saperator;
				$array[] = $comments_data;
				$i++;
			}
			$fp = fopen($filename, 'w');
			fwrite($fp, $content_to_write);
			fclose($fp);

			?>
			<div style="border:1px solid #C6C6C6; padding:10px; width:200px; text-align:center; margin:0 auto;">
				Comment deleted.
			</div>
			<br />
			<?

		}

		if($comments_data){
			foreach ($comments_arr as $comments_key => $comments_data){

				$comments_data = unserialize($comments_data);

				?>
				<div class="thumb"><a href="" target="_blank"><img src="data/thumbdata_<?=$comments_data['id']?>.jpg" width="50px" border="0" /></a></div>
				<div class="entry">
					<div class="meta">
						<b><?php echo $comments_data['name'];?></b> - <?php echo $comments_data['email'];?><?php if($comments_data['website']){echo ' (<a href="http://'.str_replace('http://','',$comments_data['website']).'" target="_blank">'.$comments_data['website'].'</a>)';}?> on <?php echo date('l, j F Y', $comments_data['time']);?>
						- <a href="?p=comments&act=delete&id=<?php echo $comments_key;?>">Delete</a>
					</div>
					<div class="comment"><?php echo $comments_data['comment'];?></div>
				</div>
				<div class="clear"></div>
				<?

			}
		}else{
			echo 'No comment.';
		}


		?>
		</div>
	</div>
	<?

}

function thumbnails(){

	if(@$_GET['act'] == 'clear'){

		$opendiryg = "data/";
		if ($handle = opendir($opendiryg)) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != ".." && substr($file, -4) == ".jpg") {

					unlink($opendiryg . $file);

				}
			}
			closedir($handle);
		}


		?>
		<div style="border:1px solid #C6C6C6; padding:10px;">
			Thumbs data cleared. Please re-generate it by browse one by one of the gallery.
		</div>
		<br />
		<?

	}

	?>
	<b>Thumbnail Maintenance</b>
	<br /><br />
	<fieldset>
		<legend><b>Clear Thumbnail Data</b></legend>
		<div style="padding:10px;">
			This will clear all thumbnails file inside 'Data' folder.
			Remember, when you remove thumbnail data, the 'Latest Added Photos' won't work until you re-generate again by browse the gallery one by one.
			<br /><br />
			<input type="button" name="remove_all_thumb_data" value="Clear Thumbnails Data" onclick="javascript:window.location='?p=thumbnails&act=clear'">
		</div>
	</fieldset>


	<?

}

function sentinal(){

}

function logout(){
	setcookie('ifotocp', '', time()-3600, '/', false);
	echo '<meta http-equiv=refresh content=0;URL=?>';
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

	/* main layout styles */

	html, body {font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #494949; background-color:#DDDDDD; text-align:center;}
	#wrapper {width: 750px; border:1px solid #CCCCCC; background-color: #EEEEEE; text-align:left; padding-bottom:20px; margin:0 auto;}

	#nav { padding:8px; background-color:#E5E5E5; border:1px solid #CCCCCC;}
	#nav ul {margin:0px 0px 0px 0px; padding:0px; list-style:none;}
	#nav li {padding:3px 10px; margin-bottom:1px; display:inline;}

	#nav a{color:#666666; text-decoration:none; font-weight:bold;}
	#nav a:hover {text-decoration:underline;}

	#content {padding:8px;}

	#header {margin-bottom: 0px;padding: 8px;}
	#header_left {float:left;}
	#header_right {text-align:right;}

	#footer {clear:both; padding:8px;}

	h3 {margin:0px; padding:0px; font-size:17px;}

	.clear {clear:both; height:1px; overflow:hidden; margin-top:-1px; padding:0; font-size:0px; line-height:0px;}

	a:link {color: #006699; text-decoration: none;}
	a:visited {text-decoration: none; color: #006699;}
	a:hover {text-decoration: underline; color: #000000;}
	a:active {text-decoration: none; color: #006699;}

	.comments {}
	.comments .list{ margin-top:10px; margin-top:10px; text-align:left; padding:5px 0px 10px 0px;}
	.comments .thumb{ float:left; margin-top:20px; background:#CCCCCC; padding:5px;}
	.comments .thumb img{ border:1px solid #666666;}
	.comments .entry {padding:5px 10px; margin-left:50px;}
	.comments .entry .meta { padding:5px 0px 5px 10px;}
	.comments .entry .comment { background:#DADADA; padding:10px; font-size:12px; line-height:20px;}
	.comments .form{ text-align:left; padding-left:100px;}
	.comments .form input{ border:1px solid #CCCCCC; color:#666666; font-family: Arial, Helvetica, sans-serif; font-size: 12px; padding:3px; width:200px;}
	.comments .form textarea{ border:1px solid #CCCCCC; color:#666666; font-family: Arial, Helvetica, sans-serif; font-size: 12px; padding:3px; width:350px; height:100px;}

	-->
	</style>
	<script>

		function pop(action, param){

			window.open('admin_popup.php?pop='+action+'&id='+ param,'Gallery','width=500,height=150,toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0');

		}

	</script>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="header_left">
					<h3><font color="#FF6600">i</font>Foto Control Panel v1.0</h3>
					My World, My Life, My Pride
				</div>
				<div id="header_right">
					<?php echo date('l, j F Y'); ?><br />
					<a href="./" target="_blank">Go to Gallery</a>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			<div id="nav">
				<ul>
					<li><a href="?">Admin Home</a></li>
				    <li><a href="?p=gallery">Gallery</a></li>
				    <li><a href="?p=comments">Comments</a></li>
				    <li><a href="?p=thumbnails">Thumbnails</a></li>
				    <!--<li><a href="?p=sentinal">Sentinal</a></li>-->
				    <li><a href="?p=logout">Logout</a></li>
				</ul>
			</div>
			<div id="content"><?php admin_contents(); ?></div>
			<div class="clear">&nbsp;</div>
		</div>
		<div id="footer">
			<div align="center">
				Powered by <a href="http://ifoto.ireans.com">iFoto</a>
				&copy; Copyright 2006 Ireans
			</div>
		</div>
	</body>
	</html>
	<?

}

function html_login(){

	?>
	<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>iFoto - My Simple Photo Gallery - Control Panel</title>
	<style type="text/css">
	<!--

	/* main layout styles */

	html, body {font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #494949; background-color:#DDDDDD; text-align:center;}
	#wrapper {width: 750px; border:1px solid #CCCCCC; background-color: #EEEEEE; text-align:left; padding-bottom:20px; margin:0 auto;}

	#nav { padding:8px; background-color:#E5E5E5; border:1px solid #CCCCCC;}
	#nav ul {margin:0px 0px 0px 0px; padding:0px; list-style:none;}
	#nav li {padding:3px 10px; margin-bottom:1px; display:inline;}

	#nav a{color:#666666; text-decoration:none; font-weight:bold;}
	#nav a:hover {text-decoration:underline;}

	#content {padding:8px;}

	#header {margin-bottom: 0px;padding: 8px;}
	#header_left {float:left;}
	#header_right {text-align:right;}

	#footer {clear:both; padding:8px;}

	h3 {margin:0px; padding:0px; font-size:17px;}

	.clear {clear:both; height:1px; overflow:hidden; margin-top:-1px; padding:0; font-size:0px; line-height:0px;}

	a:link {color: #006699; text-decoration: none;}
	a:visited {text-decoration: none; color: #006699;}
	a:hover {text-decoration: underline; color: #000000;}
	a:active {text-decoration: none; color: #006699;}

	#login_box { padding:8px; margin:0 auto; background-color:#E5E5E5; border:1px solid #CCCCCC; width:180px;}
	#login_box form { margin:0px; }
	#login_box input { border:1px solid #666666; background:#ECECEC; font-size:11px;}

	-->
	</style>
	</head>
	<body>
		<div id="wrapper">
			<div id="header">
				<div id="header_left">
					<h3><font color="#FF6600">i</font>Foto Control Panel v1.0</h3>
					My World, My Life, My Pride
				</div>
				<div id="header_right">
					<?php echo date('l, j F Y'); ?>
				</div>
				<div class="clear">&nbsp;</div>
			</div>
			<div id="nav"></div>
			<div id="content">

			<center>
			Use a valid username and password to gain access to the control panel area.
			<br /><br />
			</center>

			<div id="login_box">
				<form name="login_form" method="POST">
					Username : <input type="text" name="login" /><br />
					Password : <input type="password" name="password" /><br />
					<div align="right">
						<input type="submit" name="submit" value="Login" />
					</div>
				</form>
			</div>

			<br />
			<center>
			<a href="./">Go to Gallery</a>
			</center>

			</div>
			<div class="clear">&nbsp;</div>
		</div>
		<div id="footer">
			<div align="center">
				Powered by <a href="http://ifoto.ireans.com">iFoto</a>
				&copy; Copyright 2006 Ireans
			</div>
		</div>
		<SCRIPT LANGUAGE="JavaScript" TYPE="text/javascript">
		        document.login_form.login.focus();
		</SCRIPT>
	</body>
	</html>
	<?

}
?>