<?php

require_once 'plugin.php';

class dir extends plugin {

	/* default constructor */
	function __construct($base_dir, $directory, $filename, $extension) {
		parent::__construct($base_dir, $directory, $filename, '');
		$this->thumbnail = 'images/directory.jpg';
	}

	/* draw the object in HTML */
	public function draw() {
		return '<div class="dir_icon"><a href="?action=list&dir=' . $this->udirectory . $this->ufilename . '"><img src="' . $this->thumbnail . '" border="0"/></a></div>';
	}

	/* return an array of the object details */
	public function return_as_array() {
		$odetails = parent::return_as_array();
		$odetails['item_type'] = 'directory';
		$odetails['thumbnail'] = $this->thumbnail;
		return $odetails;
	}
}
?>
