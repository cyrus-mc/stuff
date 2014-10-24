<?php

require_once 'plugin.php';

class image extends plugin {

	protected $width, $height, $has_thumb;

	/* default constructor */
	function __construct($base_dir, $directory, $filename, $extension) {
		parent::__construct($base_dir, $directory, $filename, $extension);
		
		/* get image height and width details */
		list($this->width, $this->height) = getimagesize($this->url);

		/* generate thumbnail if required */
		$thumbnail_url = 'data/thumbdata_' . base64_encode($directory . ']|[' . $filename . ']|[' . $this->last_modified) . '.jpg';
		if (is_file($thumbnail_url)) {
			$this->thumbnail = $thumbnail_url;
			$this->has_thumb = true;
		} else {
			$this->thumbnail = 'images/img_holder.jpg';
			$this->has_thumb = false;
		}

	}

	public function get_has_thumb() { return $this->has_thumb; }

	public function set_has_thumb($has_thumb) { $this->has_thumb = $has_thumb; }

	/* draw the object in HTML */
	public function draw() {
		return '<div class="shadow">&nbsp;</div><div class="thumb"><a href="?action=view&dir=' . $this->udirectory . '&object=' . $this->get_id() . '"><img src="' . $this->thumbnail . '" border="0"/></a></div>'; 
	}

	/* return an array of the object details */
	public function return_as_array() {
		$odetails = parent::return_as_array();
		$odetails['item_type'] = 'image';
		$odetails['thumbnail'] = $this->thumbnail;
		$odetails['width'] = $this->width;
		$odetails['height'] = $this->height;
		return $odetails;
	}
}

?>
