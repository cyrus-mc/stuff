<?php
/**
 *  Implemention of a read-write semaphore
 * 
 *  vim:ts=3:sw=3:
 *
 *	@author: Matthew Ceroni
 *	@version: 1.0	
 *	
 *	@package sysvipc
 */

class rw_semaphore {
    	
	const READ_ACCESS = 0;
	const WRITE_ACCESS = 1;	
	
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
	public function __construct() {		
		$mutex_key = ftok('/home/cyrus/development/php/sysvipc/trunk/rw_semaphore.php', 'm');
		$resource_key = ftok('/home/cyrus/development/php/sysvipc/trunk/rw_semaphore.php', 'r');		
		$this->mutex = sem_get($mutex_key, 1);
		$this->resource = sem_get($resource_key, 1);		
	}
	
	/**
	 * Destructor
	 * 
	 * Remove the read/write semaphore
	 */
	public function __destruct() {
		sem_remove($this->mutex);
		sem_remove($this->resource);
	}
	
	/**
	 * Request acess to the resource
	 * 
	 * @param int $mode
	 * @return void
	 */
	private function request_access($access_type = self::READ_ACCESS) {	
		if ($access_type == self::WRITE_ACCESS) {
			sem_acquire($this->mutex);
			
			/* update the writers counter */
			$this->writers++;
			
			sem_release($this->mutex);			
			sem_acquire($this->resource);
		} else {			
			sem_acquire($this->mutex);			
			if ($this->writers > 0 || $this->readers == 0) {				
				sem_release($this->mutex);				
				sem_acquire($this->resource);				
				sem_acquire($this->mutex);				
			}
			/* update the readers counter */
			$this->readers++;
			
			sem_release($this->mutex);
		}
	}
	
	private function request_release($access_type = self::READ_ACCESS) {
		if ($access_type == self::WRITE_ACCESS) {
			sem_acquire($this->mutex);
			
			/* update the writers counter */
			$this->writers--;
			
			sem_release($this->mutex);
			sem_release($this->resource);
		} else {
			sem_acquire($this->mutex);
			
			/* update the readers counter */
			$this->readers--;
			
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