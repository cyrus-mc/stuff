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

class rw_fifo {

    const LOCK_PATH = "/tmp";
    const SHM_REF_KEY = 0;	
	const SHM_WRITE_KEY = 1;       

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
     * @access private
     * @var resource - semaphore ID
     */
    private $sem_id = null;
    
    /**
     * @access private
     * @var resource - shared memory ID
     */
    private $shm_id = null;

    /**
     * Default constructor
     * 
     * Create $lock_file if needed and open resource to it
     */
    public function __construct($resource_name) {
    	    	
    	$this->set_error_handler();
    	$this->lock_file = self::LOCK_PATH . "/rw_fifo_" . preg_replace('/[^\.\/]*\.\.\//', '', $resource_name);    	    

    	$sys_v_key = $this->string_to_int($resource_name);
    	/* create SYS V IPC semaphore and than acquire it */
    	$this->sem_id = @sem_get($sys_v_key, 1);
    	@sem_acquire($this->sem_id);
    	/* we are now in the safe zone, create lock file if necessary */
    	$this->resource = fopen($this->lock_file, "a+");
    	/* connect to shared memory and read memory keys */    	
		$this->shm_id = @shm_attach($sys_v_key, 100);
		restore_error_handler();
		/* issue, if these are not here yet we get to error handler */
	    $reference_count = @shm_get_var($this->shm_id, self::SHM_REF_KEY);
    	$write_count = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY);
    	$this->set_error_handler();
    	$reference_count += 0;
    	$reference_count += 1;
    	$write_count += 0;
    	@shm_put_var($this->shm_id, self::SHM_REF_KEY, $reference_count);
		@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, $write_count);    	
    	
    	/* we can now exit the safe zone */    	
    	@sem_release($this->sem_id);
    	restore_error_handler();
    }
    
    /**
     * Default destructor
     * 
     * Check reference count to lock_file and if zero, cleanup
     */
    public function __destruct() {
    	    	
    	if (@sem_acquire($this->sem_id)) {
	    	/* we are now in the safe zone, cleanup any necessary resources */    	
    		if ( ($reference_count = @shm_get_var($this->shm_id, self::SHM_REF_KEY)) ) {
    			$reference_count -= 1;
    			/* if there are no more references, clean up */
    			if ($reference_count == 0) {
		    		@shm_remove($this->shm_id);
    				@sem_remove($this->sem_id);    		
    				@unlink($this->lock_file);
    			} else
		    		@sem_release($this->sem_id);
    		} else
    			throw new Exception("rw_fifo::__destruct() - unable to access shared memory.");    			
    	} else
    		throw new Exception("rw_fifo::__destruct() - unable to acquire semaphore.");
    	/* close file handle */
    	fclose($this->resource);
    		    	
    }
    
    /**
     * Set class error handler
     * 
     * @return void
     */
    private function set_error_handler() { set_error_handler(array($this, 'error_handler')); }          

    /**
     * Class error handler
     * 
     * @return false
     */
    private function error_handler($errno, $errstr, $errfile, $errline) {
    	throw new Exception("rw_fifo: $errline - $errstr");    	
    	return false;
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
    	$sum_of_string = 5000000 - array_sum(array_map("ord", str_split($string)));
    	return (int)$sum_of_string;    	
    }
    
    /**
     * Acquire access to the lock file
     * 
     * @param int $lock_type
     * @param boolean $block
     * @return void
     */
    private function acquire($lock_type = LOCK_SH, $block = true) {
    	flock($this->resource, $lock_type, $block);    	
    }   

    /**
     * Release access to the lock file
     * 
     * @param boolean $block
     * @return void
     */
    public function read_release($block = true) {    	
    	$this->acquire(LOCK_UN, $block);     	
    }

    /**
	 * Request read access to the resource
	 * 
	 * @return void
	 */
    public function read_request() {    	
    	$this->set_error_handler();
    	
    	@sem_acquire($this->sem_id);
    	/* we are now in safe zone, check for writers */
    	if (@shm_get_var($this->shm_id, self::SHM_WRITE_KEY) > 0) {
    		@sem_release($this->sem_id);
    		/* grab exclusive lock to prevent readers from jumping ahead of writers */    		
    		$this->acquire(LOCK_EX); 
    		/* once we gain access, release and grab shared lock */
    		$this->acquire(LOCK_UN);    			
    	} else
    		sem_release($this->sem_id);    	
    	
    	/* acquire a shared lock */
    	$this->acquire();
    	
    	/* restore the old error handler */
    	restore_error_handler();
    }     
 
    /**
	 * Request write access to the resource
	 * 
	 * @return void
	 */
    public function write_request() {
    	$this->set_error_handler();
    	
    	@sem_acquire($this->sem_id);
    	/* we are now in the safe zone, update writers */    		
    	$writers = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY);
    	$writers = $writers + 1;
    	@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, $writers);
    	@sem_release($this->sem_id);
    	/* acquire an exclusive lock */
    	$this->acquire(LOCK_EX);
    	
    	/* restore the old error handler */
    	restore_error_handler();
    }
    
    /**
     * Release write access to the resource
     * 
     * @return void
     */
    public function write_release() {
    	$this->set_error_handler();
    	
    	@sem_acquire($this->sem_id);
    	/* we are now in the safe zone, update writers */    		
    	$writers = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY);
    	$writers = $writers - 1;
    	@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, $writers);
    	sem_release($this->sem_id);
    	/* release current lock */
    	$this->acquire(LOCK_UN);

    	/* restore the old error handler */
    	restore_error_handler();
    }
}
?>