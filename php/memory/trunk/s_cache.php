<?php
/**
 *  Implemention of memory cache
 * 
 *  vim:ts=3:sw=3:
 *
 *	@author: Matthew Ceroni
 *	@version: 1.0	
 *	
 *	@package memory
 */

require_once 'cache.php';

class s_cache extends cache {	
	
	/**
	 * @access private
	 * @var int - holds size of cache
	 */
	private $max_cache_lines = 0;
	
	private $cur_cache_lines = 0;	
		
	/**
	 * Default constructor
	 * 
	 * Initialize the size of the cache and zero out all cache lines
	 * 
	 * @param int $size
	 * @return void
	 */
	public function __construct($size) {
		/* call parent constructor */
		parent::__construct();
		
		/* initialize the size */
		$this->max_cache_lines = $size;		
	}
	
	/**
	 * Add element to the cache. Will remove oldest element, based on access time, if size exceeds
	 * set limit
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @return int
	 */
	public function add($key, $data, $overwrite = false) {		
		
		/* check if overwrite is true or that the key does not already exist */
		if ($overwrite || ! $this->cache_lines[$key]) {			
			/* check to see if cache is at maximum size */		
			if ($this->max_cache_lines != 0 && $this->cur_cache_lines == $this->max_cache_lines) {
				/* advance pointer to the end of the array */				
				end($this->cache_lines);
				if (! $this->remove(key($this->cache_lines)))																		
					return false;				
			}
			$this->cache_lines = array_merge(array($key => array('contents' => $data, 'dirty' => false)), $this->cache_lines);
			$this->cur_cache_lines++;
			return true;
		}
		self::$errstr = "s_cache::add($key, ...) - overwrite = $overwrite - failed to add data to cache.";
		return false;		
	}			
		
	/**
	 * Retrieve element from cache referenced by key. Update cache access times array
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key, $return_dirty = false) {
		if (($element = parent::get($key, $return_dirty))) {
			$this->update_access_time($key);
			return $element;
		}
		return false;
	}
	
	/**
	 * Set the contents of cache referenced by $key
	 * 
	 * @param string $key
	 * @param mixed $data	 
	 * @return boolean
	 */
	public function set($key, $data) {
		if (parent::set($key, $data)) {
			$this->update_access_time($key);
			return true;
		}
		return false;
	}
	
	/**
	 * Remove an element from the cache, specified by the key
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		if (parent::remove($key)) {			
			$this->cur_cache_lines--;
			return true;
		}
		return false;
	}		
	
	/**
	 * Move element with $key to top of cache_atimes list
	 * 
	 * @param string $key
	 * @return void
	 */
	private function update_access_time($key) {
		/* move element with $key to top of cache */			
		$element_to_move = $this->cache_lines[$key];
		unset($this->cache_lines[$key]);		
		$this->cache_lines = array_merge(array($key => $element_to_move), $this->cache_lines);		
	}
	
	/**
	 * Return the key to cache line with the oldest access time
	 * 
	 * @return string
	 */
	protected function get_oldest_cache() {
		/* set pointer to end of array */
		end($this->cache_lines); 
		return key($this->cache_lines); 
	}
	
	/**
	 * Return the key to cache line with the most recent access time
	 * 
	 * @return string
	 */
	protected function get_newest_cache() {
		/* set pointer to beginning of array */
		reset($this->cache_lines);
		return key($this->cache_lines); 		
	}
	
	/**
	 * Return the current number of keys in the cache
	 * 
	 * @return int
	 */
	public function get_current_size() { return $this->cur_cache_lines; }
	
	/**
	 * Return the maximum number of cache lines
	 * 
	 * @return int
	 */
	public function get_max_size() { return $this->max_cache_lines; }	

	/**
	 * Set the maximum number of cache lines
	 * 
	 * @param int $size
	 */
	public function set_max_size($size) { $this->max_cache_lines = $size; }
	
}

?>