<?php

require_once 'plugin.php';

class mp3 extends plugin {

	/* default constructor */
	function __construct($base_dir, $directory, $filename, $extension) {
		parent::__construct($base_dir, $directory, $filename, $extension);
		$this->thumbnail = 'images/mp3.png';
	}

	/* draw the object in HTML */
	public function draw() {
		return '<div class="dir_icon"><a href="' . $this->url . '"><img src="' . $this->thumbnail . '" border="0"/></a></div>';
	}

	/* return an array of the object details */
	public function return_as_array() {
		$odetails = parent::return_as_array();
		$odetails['item_type'] = 'mp3';
		$odetails['thumbnail'] = $this->thumbnail;

		return $odetails;
	}
}

?>
