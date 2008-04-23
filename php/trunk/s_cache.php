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
	 * @access private
	 * @var array - references to cache lines, used to record access time
	 */
	private $cache_atimes = array();	
	
	/**
	 * Default constructor
	 * 
	 * Initialize the size of the cache and zero out all cache lines
	 * 
	 * @param int $size
	 * @return void
	 */
	public function __construct($size) {		
		/* call base constructor */
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
	 * @return boolean
	 */
	public function add($key, $data) {
		/* check to make sure cache size is not exceeded */
		if ($this->max_cache_lines != 0 && $this->cur_cache_lines == $this->max_cache_lines) {
			if (! $this->remove($this->cache_atimes[$this->max_cache_lines - 1]))
				return false;							
		}
		
		if (parent::add($key, $data)) {			
			/* add reference key to memory to the cache_atimes array */
			$this->cur_cache_lines++;			
			array_unshift($this->cache_atimes, $key);								
		} else
			return false;
		
		return true;
	}
	
	/**
	 * Retrieve element from cache referenced by key. Update cache access times array
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		$element = parent::get($key);		
		/* if $element is not null, meaning it is in the cache, update access times */
		if (isset($element)) {	
			$this->update_access_time($key);					
		}
		return $element;
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
			unset($this->cache_atimes[array_search($key, $this->cache_atimes)]);
			$this->cur_cache_lines--;
			return true;
		}
		return false;
	}
	
	protected function get_oldest_element() { return $this->cache_atimes[count($this->cache_atimes) - 1]; }
	
	/**
	 * Move element with $key to top of cache_atimes list
	 * 
	 * @param string $key
	 * @return void
	 */
	private function update_access_time($key) {
		/* move element with $key to top of cache_atimes */
		/* don't really like the loop, will try to improve */
		unset($this->cache_atimes[array_search($key, $this->cache_atimes)]);
		array_unshift($this->cache_atimes, $key);
	}
	
	/**
	 * Return the current number of keys in the cache
	 * 
	 * @return int
	 */
	public function get_current_size() { return $this->cur_cache_lines; }
	
	/**
	 * Return the maximum number of keys that can exist in the cache
	 * 
	 * @return int
	 */
	public function get_max_size() { return $this->max_cache_lines; }
	
	public function print_atimes() { print_r($this->cache_atimes);}
	public function print_size() { print $this->cur_cache_lines; }
	
}

?>