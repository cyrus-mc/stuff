<?php

abstract class plugin {
	
	protected $extension, $filesize, $last_modified, $directory, $filename, $url, $thumbnail, $id;
	protected $base_dir;
	protected $prev_object, $next_object;

	// Force extending class to define these methods
	abstract protected function draw();

	/* default class (remember to call this in child classes */
	public function __construct($base_dir, $directory, $filename, $extension) {

		$this->base_dir = $base_dir;
		$this->url = $base_dir . $directory . $filename;	
		$this->directory = $directory;
		$this->udirectory = urlencode($directory);
		$this->filename = $filename;
		$this->ufilename = urlencode($filename);

		$this->filesize = filesize($this->url);
		$this->last_modified = filectime($this->url);

		$this->extension = $extension;
		$this->id = md5($this->directory . $this->filename);
	}

	public function get_url() {
		return $this->url;
	}

	public function set_next(& $next_object) { $this->next_object = $next_object; }
	public function get_next() { return $this->next_object; }

	public function set_prev(& $prev_object) { $this->prev_object = $prev_object; }
	public function get_prev() { return $this->prev_object; }

	public function get_id() { return $this->id; }

	public function set_thumbnail($thumbnail) { $this->thumbnail = $thumbnail; }

	public function get_thumbnail() { return $this->thumbnail; }

	public function return_as_array() {
		return array('filesize' => $this->filesize,
			'last_modified' => $this->last_modified,
			'directory' => $this->directory,
			'udirectory' => $this->udirectory,
			'filename' => $this->filename,
			'ufilename' => $this->ufilename,
			'id' => md5($this->directory . $this->filename.time()) );
	}

	public function get_extension() { return $this->extension; }
}

?>
