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

class cache {
	
	/**
	 * @access protected
	 * @var array - cache
	 */
	protected $cache = null;
	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	static protected $errstr = "";
	
	/**
	 * Default constructor
	 * 
	 * Initialize the size of the cache and zero out all cache lines
	 */
	public function __construct() {			
		$this->clear_cache();	
	}
	
	/**
	 * Clear the cache
	 * 
	 * @return void
	 */
	public function clear_cache() {
		unset($this->cache);
		$this->cache = array();
	}
	
	/**
	 * Add @data to cache, using the specified $key. Overwrite if found if
	 * $overwrite = true
	 * 
	 * @param string $key
	 * @param mixed $data
	 * @param boolean $overwrite
	 * @return boolean
	 */
	public function add($key, $data, $overwrite = false) {
		print "adding key = $key\n";
		/* check if key already exists and overwrite if true */		
		if ($overwrite || ! array_key_exists($key, $this->cache)) {			
			$this->cache[$key] = array('contents' => $data, 'dirty' => false);
			return true;			
		}
		self::$errstr = "cache::add($key, ...) - overwrite = $overwrite and key already exists";
		return false;
	}
	
	/**
	 * Return the cache contents stored by reference $key
	 * 
	 * @param string $key
	 * @return mixed
	 */
	public function get($key) {
		return $this->cache[$key]; 
	}
	
	/**
	 * Set the contents of cache referenced by $key
	 * 
	 * @param string $key
	 * @param mixed $data	 
	 * @return boolean
	 */	
	public function set($key, $data) {
		/* check to see if key exists, if not add if true */
		if (array_key_exists($key, $this->cache)) {
			 $this->cache[$key]['contents'] = $data;
			 $this->cache[$key]['dirty'] = false;
			 return true;
		} 
		self::$errstr = "cache::set($key, ...) - key does not exist";
		return false;
	}
	
	/**
	 * Remove the contents of the cache referenced by $key
	 * 
	 * @param string $key
	 * @return boolean
	 */
	public function remove($key) {
		if (array_key_exists($key, $this->cache)) {
			unset($this->cache[$key]);
			return true;
		}
		self::$errstr = "cache::remove($key, ...) - key does not exist";
		return false;
	}
	
	/**
	 * Mark a cache line dirty
	 * 
	 * @param string $key
	 * @return void
	 */
	public function set_dirty($key) {
		$this->cache[$key]['dirty'] = true;
	}
	
	/**
	 * Mark multiple cache lines dirty
	 * 
	 * @param array keys
	 * @return void
	 */
	public function set_m_dirty(array $keys) {
		foreach ($keys as $key) {
			$this->set_dirty($key);
		}
		
	}
	
	/**
	 * Print the error string
	 * 
	 * @return void
	 */
	public function print_err() { print self::$errstr . "\n"; }
	
	/**
	 * Print contents of cache array
	 * 
	 * @return void
	 */
	public function print_cache() {
		print_r($this->cache);
	}
}

?>