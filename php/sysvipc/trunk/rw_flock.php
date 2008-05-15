<?php
/**
 * Implemention of a read-write semaphore (using files) that can be used amongst 
 * seperate PHP processes
 * 
 * vim:ts=3:sw=3:
 *
 * @author: Matthew Ceroni
 * @version: 1.0
 * 
 * TODO: add correct error handling 
 * 
 * @package sysvipc
 */

class rw_flock {

    const LOCK_PATH = "/tmp";       

    /**
     * @access private
     * @var string - path and filename to lock file
     */
    private $lock_file;
    
    /**
     * @access private
     * @var resource - currently held resource
     */
    private $resource = null;
    
    /**
     * @access privae
     * @var resource - semaphore ID
     */
    private $sem_id = null;
        	
	/**
	 * @access private
	 * @var string - holds the latest error message
	 */
	protected $errstr = "";
	
	/**
	 * @access protected
	 * @var boolean
	 */
	protected $no_error = true;
    
    /**
     * Default constructor
     * 
     * Create $lock_file if needed and open resource to it
     */
    public function __construct($resource_name) {
    	    	
    	$this->lock_file = self::LOCK_PATH . "/rw_flock_" . preg_replace('/[^\.\/]*\.\.\//', '', $resource_name);    	    

    	/* create SYS V IPC semaphore and acquire it */
    	if ( ($this->sem_id = @sem_get($this->string_to_int($resource_name), 1)) && @sem_acquire($this->sem_id)) {
	    	/* open the file for reading and writing and try to read the contents */    		
	    	if ( ($this->resource = fopen($this->lock_file, 'a+')) ) {	    		
	    		$references = fgets($this->resource);
	    		/* add zero incase the file was empty, this initializes it to 0 */
	    		$references += 0;
	    		$references += 1;
	    		/* truncate the file and then write back the new reference count */
	    		if ( (ftruncate($this->resource, 0) && fwrite($this->resource, $references)) == false )
	    			$this->set_error("rw_flock::__construct($resource_name) - unable to write back reference count.");	    		
	    	}		    		        		
    		/* we can now exit the safe zone */
    		sem_release($this->sem_id);    			
    	} else
    		$this->set_error("rw_flock::__construct($resource_name) - unable to create and acquire semaphore");

    	/* check if there was an error during construction, if so throw an exception */
    	if ($this->raise_error() == false)
    		throw new Exception($this->errstr);
    }
    
    /**
     * Default destructor
     * 
     * Check reference count to lock_file and if zero, cleanup
     * 
     * TODO: clean up this function
     */
    public function __destruct() { 
    	/* enter safe zone */    	
    	if (@sem_acquire) {    		
    		/* read the contents of lock_file to determine the reference count */
    		/* move to beginning of the file */
    		if ( (!fseek($this->resource, 0) && ($references = fgets($this->resource))) )  {
    			$references -= 1;
    			if ( (ftruncate($this->resource, 0) && fwrite($this->resource, $references)) == false )
	    			$this->set_error("rw_flock::__destruct() - unable to write back reference count.");
			    			
	    		if ($references == 0) {    				
    				/* if errors occur here there isn't much we can do so ignore */
    				@sem_remove($this->sem_id);
    				@unlink($this->lock_file);
    			}
    		} else    			    	
    			$this->set_error("rw_flock::__destruct() - unable to read current reference count.");    			
    	} else
    		$this->set_error("rw_flock::__destruct() - unable to acquire semaphore, not updating reference count");

    	@fclose($this->resource);
    	/* check if there was an error during destruction, if so throw an exception */
    	if ($this->raise_error() == false)
    		throw new Exception($this->errstr);
    }
    
    /**
     * Generate an integer value based on a string
     * 
     * (generates a number between 1 and 10 million)
     * 
     * @param $string
     * @return int
     */
    private function string_to_int($string) {
    	$sum_of_string = 5000000 - array_sum(array_map("ord", str_split($sem_key)));
    	return (int)$sum_of_string;    	
    }
    
    /**
     * Acquire access to the lock file
     * 
     * @param int $lock_type
     * @param boolean $block
     * @return boolean
     */
    private function acquire($lock_type = LOCK_SH, $block = true) {
    	return flock($this->resource, $lock_type, $block);    	
    }   

    /**
     * Release access to the lock file
     * 
     * @param boolean $block
     * @return void
     */
    public function release($block = true) {    	
    	return $this->acquire(LOCK_UN, $block); 
    }

    /**
	 * Request read access to the resource
	 * 
	 * @return void
	 */
    public function read() { $this->acquire(); }       
 
    /**
	 * Request write access to the resource
	 * 
	 * @return void
	 */
    public function write() { $this->acquire(LOCK_EX); }

    /**
	 * Set the error string and raise error flag
	 * 
	 * @param string - error description
	 * @return void;
	 */
	private function set_error($string) {
		$this->errstr = $string;
		$this->no_error = false;
	}
	
	/**
	 * Return the error flag (true means no error, false indicates failure)
	 * 
	 * @return boolean
	 */
	private function raise_error() {
		/* save the current error flag */
		$current_err_flag = $this->no_error;
		/* reset error flag to true */
		$this->no_error = true;
		return $current_err_flag;
	}
	
	/**
	 * Return the current error string
	 * 
	 * @return string	 
	 */
	public function get_errstr() { return $this->errstr; }	
}
?>