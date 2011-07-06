<?php

namespace core\tools;

class Linker {
	protected $master;
	protected $slaves;
	
	public function __construct() {

	}
	
	public function setMaster($master) {
		$this->master = $master;
	}
	
	public function addSlave($slave){
		$this->slaves[] = $slave;
	}
	
	public function setSlaves($slaves) {
		if( !is_array( $slaves ) ) {
			$slaves = array( $slaves );
		}
		$this->slaves = $slaves;
	}
	
	public function getMaster() {
		return $this->master;
	}
	
	public function getSlaves() {
		return $this->slaves;
	}
}