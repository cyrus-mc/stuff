<?php
/**
 * Implemention of memory cache
 * 
 * vim:ts=3:sw=3:
 *
 * @author: Matthew Ceroni
 * @version: 1.0	
 *	
 * @package memory
 */


class cache {
	
	/**
	 * @access protected
	 * @var array - cache
	 */
	protected $cache_lines = null;
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	protected $errstr = "";		
	
	/**
	 * Default constructor
	 * 
	 * Initialize the size of the cache and zero out all cache lines
	 */
	public function __construct() {
		$this->clear_cache();			
	}	
	
	/**
	 * @access private
	 * @var int - holds the number of clean cache hits
	 */
	private $clean_cache_hits = 0;
	
	/**
	 * @access private
	 * @var int - holds the number of dirty cache hits
	 */
	private $dirty_cache_hits = 0;
	
	/**
	 * @access private
	 * @var int - holds the number of cache misses
	 */
	private $cache_misses = 0;
	
	/* begin definition of abstract members */
	
	/**
	 * Add @data to cache, using the specified $key, overwrite = true if found
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public function add($key, $data, $overwrite = false) {			
		if ($overwrite || ! isset($this->cache_lines[$key])) {
			$this->cache_lines[$key] = array('contents' => $data, 'dirty' => false);
			return true;
		}
		 
		self::$errstr = "cache::add($key, ...) - overwrite = $overwrite - key already exists in cache.";		
		return false;
	}
	
	/* end definition of abstract members */
	
	/**
	 * Return the cache line stored by reference $key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key, $return_dirty = false) {				
		if (isset($this->cache_lines[$key])) {
			$element = $this->cache_lines[$key];
			/* update cache counters */
			$this->dirty_cache_hits += (int) $element['dirty'];
			$this->clean_cache_hits += (int) !$element['dirty'];
			if ($element['dirty'] && !$return_dirty) {
				self::$errstr = "cache::get($key, $return_dirty) - specified key found but cache dirty.";
				return false;				
			}
			return $element;
		} 
				
		$this->cache_misses++;		
		return null;
	}		
	
	/**
	 * Set the contents of the cache line referenced by $key
	 * 
	 * @param string $key
	 * @param mixed $data	 
	 * @return boolean
	 */	
	public function set($key, $data) {		
		if (isset($this->cache_lines[$key])) {			
			$this->cache_lines[$key]['contents'] = $data;
			$this->cache_lines[$key]['dirty'] = false;
			return true;
		} 
		
		self::$errstr = "cache::set($key, ...) - key does not exist.";
		return false;
	}					
	
	/**
	 * Clear all cache lines - effectively initializing the cache
	 * 
	 * @return void
	 */
	public function clear_cache() {		
		unset($this->cache_lines);
		$this->cache_lines = array();		
	}
	
	/**
	 * Remove the cache line referenced by $key
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {		
		if (isset($this->cache_lines[$key])) {
			unset($this->cache_lines[$key]);
			return true;
		} 
		
		self::$errstr = "cache::remove($key, ...) - key does not exist in cache.";
		return false;
	}
	
	/**
	 * Mark a cache line dirty
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function set_dirty($key) {		
		if (isset($this->cache_lines[$key])) {			
			$this->cache_lines[$key]['dirty'] = true;
			return true;			
		}
		
		self::$errstr = "cache::set_dirty($key) - key does not exist in cache.";
		return false;
	}
	
	/**
	 * Mark multiple cache lines dirty
	 * 
	 * @param array keys
	 * @return void
	 */
	public function set_m_dirty(array $keys) {
		foreach ($keys as $key)
			if ( !($return_value = $this->set_dirty($key)) )
				return $return_value;
						
		return true;		
	}
	
	/**
	 * Return the number of clean cache hits
	 * 
	 * @return int
	 */
	public function get_clean_cache_hits() { return $this->clean_cache_hits; }
	
	/**
	 * Return the number of dirty cache hits
	 * 
	 * @return int
	 */
	public function get_dirty_cache_hits() { return $this->dirty_cache_hits; }
	
	/**
	 * Return the number of cache misses
	 * 
	 * @return int
	 */
	public function get_cache_misses() { return $this->cache_misses; }
	
	/**
	 * Reset the internal counts for clean and dirty cache hits to zero
	 * 
	 * @return void
	 */
	public function reset_counters() {
		$this->clean_cache_hits = 0;
		$this->dirty_cache_hits = 0;
		$this->cache_misses = 0;		
	}
		
	/**
	 * Return the current error string
	 * 
	 * @return string	 
	 */
	public function get_errstr() { return $this->errstr; }	
	
	/**
	 * Print contents of cache
	 * 
	 * @return void
	 */
	public function print_cache() {
		print_r($this->cache_lines);
	}
}

?>