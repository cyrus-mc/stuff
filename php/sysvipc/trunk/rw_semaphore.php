<?php
/**
 *  Implemention of a read-write semaphore that can be used amongst seperate PHP processes
 * 
 *  vim:ts=3:sw=3:
 *
 * @author: Matthew Ceroni
 * @version: 1.0
 * 
 * TODO: add correct error handling
 * 
 * When there is a writer waiting, have it grab a mutex sem. When subsequent
 * readers come in, check the writers variable, if > 1, have it try to grab 
 * that mutex sem. This should implement a writers first queue 
 * 
 * @package sysvipc
 */

class rw_semaphore {
    	
	const READ_ACCESS = 0;
	const WRITE_ACCESS = 1;	
	
	const SHM_REF_KEY = 0;
	const SHM_READ_KEY = 1;
	const SHM_WRITE_KEY = 2;	
	
	/**
	 * @access private
	 * @var resource - mutex semaphore
	 */
	private $mutex;
	
	/**
	 * @access private
	 * @var resource - read/write semaphore
	 */
	private $resource;
	
	/**
	 * @access private
	 * @var int - shared memory identifier
	 */
	private $shm_id;

	/**
	 * @access private
	 * @var int
	 */
	private $writers = 0;	
	
	/**
	 * @access private
	 * @var int
	 */
	private $readers = 0;	

	/**
	 * Default constructor
	 * 
	 * Initialize the read/write semaphore
	 */
	public function __construct($file) {	
		$mutex_key = ftok($file, 'm');
		$resource_key = ftok($file, 'r');
		$shm_key = ftok($file, 's');
		
		$this->mutex = sem_get($mutex_key, 1);
		$this->resource = sem_get($resource_key, 1);
		
		$this->shm_id = shm_attach($shm_key, 1024);		
						
		/* initialize the shared memory section if needed */
		sem_acquire($this->mutex);
		if (! ($ref_count = @shm_get_var($this->shm_id, self::SHM_REF_KEY)) ) {
			@shm_put_var($this->shm_id, self::SHM_REF_KEY, 1);									
			@shm_put_var($this->shm_id, self::SHM_READ_KEY, 0);
			@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, 0);							
		} else {
			/* increment the reference count */			
			$ref_count += 1;
			@shm_put_var($this->shm_id, self::SHM_REF_KEY, $ref_count);			
		}
		sem_release($this->mutex);		
	}
	
	/**
	 * Destructor
	 * 
	 * Remove the read/write semaphore
	 */
	public function __destruct() {					
		/* grab the references variable */
		sem_acquire($this->mutex);
		$references = @shm_get_var($this->shm_id, self::SHM_REF_KEY);		
		$references -= 1;
		@shm_put_var($this->shm_id, self::SHM_REF_KEY, $references);		
				
		if ($references == 0) {
			print "in destructor - no more references - destroying\n";						
			/* destroy the semaphores */
			shm_remove($this->shm_id);
			sem_remove($this->resource);
			sem_remove($this->mutex);			
		} else 
			sem_release($this->mutex);		
	}
	
	/**
	 * Request acess to the resource
	 * 
	 * @param int $access_type
	 * @return void
	 */
	private function request_access($access_type = self::READ_ACCESS) {				
		
		if ($access_type == self::WRITE_ACCESS) {
			sem_acquire($this->mutex);
			
			$this->writers = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY);
			$this->writers += 1;
			@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, $this->writers);
			sem_release($this->mutex);
									
			sem_acquire($this->resource);
		} else {				
			sem_acquire($this->mutex);
			$this->writers = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY); 
			$this->readers = @shm_get_var($this->shm_id, self::SHM_READ_KEY);
			
			print "current readers value = $this->readers\n";
			if ($this->writers > 0 || $this->readers == 0) {				
				sem_release($this->mutex);				
				sem_acquire($this->resource);
				sem_acquire($this->mutex);
			}			
			/* update the readers counter */			
			$this->readers += 1;
			@shm_put_var($this->shm_id, self::SHM_READ_KEY, $this->readers);
			
			sem_release($this->mutex);
		}
						
	}
	
	/**
	 * Release access to the resource
	 * 
	 * @param int $access_type
	 * @return void
	 */
	private function request_release($access_type = self::READ_ACCESS) {			
		
		if ($access_type == self::WRITE_ACCESS) {			
			sem_acquire($this->mutex);
			$this->writers = @shm_get_var($this->shm_id, self::SHM_WRITE_KEY);
			$this->writers -= 1;
			@shm_put_var($this->shm_id, self::SHM_WRITE_KEY, $this->writers);
			sem_release($this->mutex);
			
			sem_release($this->resource);
		} else {
			
			sem_acquire($this->mutex);
			$this->readers = @shm_get_var($this->shm_id, self::SHM_READ_KEY);
			$this->readers -= 1;
			@shm_put_var($this->shm_id, self::SHM_READ_KEY, $this->readers);

			print "current readers value = $this->readers\n";
			if ($this->readers == 0)
				sem_release($this->resource);
			
			sem_release($this->mutex);
		}
						
	}
	
	/**
	 * Request read access to the resource
	 * 
	 * @return void
	 */
	public function read_access() { $this->request_access(self::READ_ACCESS); }
	
	/**
	 * Release read access to the resource
	 * 
	 * @return void
	 */
	public function read_release() { $this->request_release(self::READ_ACCESS); }
	
	/**
	 * Request write access to the resource
	 * 
	 * @return void
	 */
	public function write_access() { $this->request_access(self::WRITE_ACCESS); }
	
	/**
	 * Release write access to the resource
	 * 
	 * @return void
	 */
	public function write_release() { $this->request_release(self::WRITE_ACCESS); }
    
}
?>