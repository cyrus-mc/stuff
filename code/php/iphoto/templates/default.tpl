<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
	<title>iPhoto - Gallery Viewer</title>
	<link href="includes/lightbox/lightbox.css" rel="stylesheet" type="text/css" />

	<script type="text/javascript" src="includes/lightbox/lightbox.js"></script>
	<script type="text/javascript">
		function popup_latestupdate() {
			newwindow2=window.open('','name','height=270,width=150');
			var tmp = newwindow2.document;
			tmp.write('<html><head><title>Latest Added Photos</title>');
			tmp.write('<style type="text/css">html, body {font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #494949; background-color:#DDDDDD; text-align:center;}</style>');
			tmp.write('</head><body><p><b>Notes About Latest Added Photos</b></p>');
			tmp.write('The latest photos list are based on File Creation Date/Time in UNIX timestamp format, and after thumbnail image created.<br />On Windows platform, see file \'Properties\' and \'File Created\' attribute.<br /><br />PHP Ref : filectime();');
			tmp.write('<p><a href="javascript:self.close()">Close</a></p>');
			tmp.write('</body></html>');
			tmp.close();
		}

		function setActive() {
			var query_string = location.search;
			alert(query_string);
			var aquery = new Array();
			aquery = query_string.split('&');
			var mid = aquery[1].split('=')[1];
			var mobject = document.getElementById(mid);
			mobject.className = 'active';
		}
	</script>

	<style type="text/css">
		html, body {
			font-family: Arial, Helvetica, sans-serif; 
			font-size: 11px; 
			color: #494949; 
			background-color: #DDDDDD; 
			text-align: center;
		}

		#wrapper {
			width: 750px; 
			border: 1px solid #CCCCCC; 
			background-color: #EEEEEE; 
			text-align: left; 
			padding-bottom: 20px; 
			margin: 0 auto;
		}

		#nav { 
			width: 130px; 
			float: left; 
			margin-left: -1px; 
			background-color: #E5E5E5; 
			border: 1px solid #CCCCCC; 
			line-height:18px;
		}

		#nav ul {
			margin: 0px 0px 10px 0px; 
			padding: 0px; 
			list-style: none;
		}

		#nav li {
			padding: 1px 10px; 
			margin-bottom: 1px;
		}

		#nav li.home_button {
			background-color: #CCCCCC; 
			margin-bottom: 5px; 
			list-style: none;
		}

		#nav li.active {
			background-color: #DADADA; 
			list-style: none; 
			border-bottom: 1px solid #CCCCCC; 
			margin-bottom: 0px;
		}

		#nav li.notactive {
			margin-left: 10px;
			font-weight: normal;
		}

		#nav a {
			color: #666666; 
			text-decoration: none; 
		}

		#nav a:hover {
			text-decoration: underline;
		}

		#content {
			margin-left: 140px; 
			padding: 0px 8px 8px 8px;
		}

		#header { 
			margin-bottom: 0px; padding: 8px;
		}

		#header_left {
			float: left;
		}

		#header_right {
			text-align: right;
		}

		#footer { 
			clear: both; padding: 8px;
		}

		h3 {
			margin: 0px; 
			padding: 0px; 
			font-size: 17px;
		}

		#homepage {
			text-align: center;
		}

		#homepage .latest {}

		#homepage .latest .thumb_wrapper { 
			margin: auto;
		}

		.clear {
			clear: both; 
			height: 1px; 
			overflow: hidden; 
			margin-top: -1px; 
			padding: 0; 
			font-size: 0px; 
			line-height: 0px;
		}

		a:link { 
			color: #006699; 
			text-decoration: none;
		}

		a:visited { 
			text-decoration: none; 
			color: #006699;
		}

		a:hover {
			text-decoration: underline; 
			color: #000000;
		}

		a:active { 
			text-decoration: none; 
			color: #006699;
		}

		.thumb_wrapper { 
			text-align:center;
		}

		.thumb_wrapper .container {}

		.thumb_wrapper .shadow { 
			background: url("templates/default/shadow_1.jpg") bottom no-repeat;
		}

		.thumb_wrapper .thumb {
			background: url("templates/default/shadow_2.jpg") bottom no-repeat; 
			padding-bottom: 14px;
		}

		.thumb_wrapper .title { 
			width: 130px; 
			background: #E5E5E5;
			border: 1px solid #CCCCCC; 
			margin: 0px 3px 3px; 
			padding: 3px; 
			color: #999999; 
			font-weight: bold;
		}

		.tile { 
			float: left; 
			width: 147px;
		}

		.tile_table {
			width: 147px;
		}

		#fullsize {
			text-align: center;
		}

		#fullsize .menu { 
			width: 550px; 
			margin: auto; 
			height:20px; 
		}

		#fullsize .menu .left {
			float: left; 
			width: 50%; 
			text-align: left;
		}

		#fullsize .menu .right {
			float: left; 
			width: 50%; 
			text-align: right;
		}

		#fullsize .image_wrapper {} 
		
		#fullsize .image_wrapper .shadow { 
			background: url("templates/default/shadow_1_one.jpg") bottom no-repeat;
		} 
		
		#fullsize .image_wrapper .image {
			background: url("templates/default/shadow_2_one.jpg") bottom no-repeat; 
			padding-bottom: 14px;
		} 
		
		#fullsize .title {
			margin: auto;
			width: 550px; 
			background: #E5E5E5; 
			border: 1px solid #CCCCCC; 
			margin-top: 5px;
			padding: 3px; 
			color: #999999; 
			font-weight: bold;
		} 
		
		#fullsize .description {
			width: 550px; 
			margin-bottom: 10px; 
			padding: 10px; 
			font-size: 11px;
			border-bottom: 1px dashed #CCCCCC;
		} 
		
		#fullsize .footer {
			width: 550px;
			margin: auto;
		} 
		
		#fullsize .footer .prev {
			width: 147px; 
			float: left;
		} 
		
		#fullsize .footer .desc { 
			width: 256px; 
			float: left; 
			padding-top: 20px; 
			text-align: left;
			color: #999999;
		} 
		
		#fullsize .footer .desc ul{ 
			list-style: none;
		} 
		
		#fullsize .footer .next {
			width: 147px; 
			float: left;
		} 
		
		.directory { 
			margin: 8px 0px;
		} 
		
		.latest p {
			width: 180px; 
			background: #E5E5E5; 
			border: 1px solid #CCCCCC; 
			margin: 20px 3px 3px;
			padding: 3px; 
			font-size: 11px; 
			color: #999999; 
			font-weight: bold;
		} 
		
		
		.comments .list { 
			border-top: 1px dashed #CCCCCC; 
			margin-top: 10px;
			border-bottom: 1px dashed #CCCCCC; 
			margin-top: 10px; 
			text-align: left; 
			padding: 5px 0px 10px 0px;
		} 
		
		.comments .entry { 
			padding:5px 10px;
		} 
		
		.comments .entry .meta {
			padding:5px 0px 5px 10px;
		} 
		
		.comments .entry .comment { 
			background:#E3E3E3;
			padding: 10px; 
			font-size: 12px; 
			line-height: 20px; 
			color: #666666;
		} 
		
		.comments .form { 
			text-align: left; 
			padding-left: 100px;
		} 
		
		.comments .form input { 
			border: 1px solid #CCCCCC; 
			color: #666666; 
			font-family: Arial, Helvetica, sans-serif;
			font-size: 12px; 
			padding: 3px; 
			width: 200px;
		} 
		
		.comments .form textarea {
			border: 1px solid #CCCCCC; 
			color: #666666; 
			font-family: Arial, Helvetica, sans-serif; 
			font-size: 12px; 
			padding:3px; 
			width: 350px; 
			height: 100px;
		} 
	</style>
</head> 
<body> 
	<div id="wrapper"> 
		<div id="header"> 
			<div id="header_left"><h3><font color="#FF6600">i</font>Photo</h3></div>
			<div id="header_right">
			</div>
			<div class="clear">&nbsp;</div>
		</div>
		<div id="nav">
			<ul><li class="home_button"><a href="?"><b>Home</b></a></li>
				MENU
			</ul>
		</div>
		<div id="content">
			<table align="center" width="100%" border="0" cellpadding="0" cellspacing="0">
				MAIN
			</table>
		</div>
		<div class="clear">&nbsp;</div>
	</div>
	<div id="footer">
		<div align="center"> Powered by <a href="">iPhoto</a> &copy; Copyright 2006 - <a href="admin.php">Administration Panel</a></div>
	</div>
	<iframe id="thumbgen" name="thumbgen" style="border: 0px none; width: 0px; height: 0px" src="thumb_generator.php?dir={TDIR}"></iframe>
</body>
</html>
