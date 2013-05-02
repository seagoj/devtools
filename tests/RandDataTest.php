<?php

class randDataTest extends PHPUnit_Framework_TestCase
{
	private $randData;
	
	public function __construct() {
		$this->randData = new randData();
		$this->testing($this->randData);
		$this->with($this);
		$this->runTest();
	}
	public function __constructTest() {
		return true;
	}
	public function getTest() {
		$types = array('string','array','integer','bool','double','null');
		$result = true;
		foreach($types AS $type) {
			$func = 'is_'.$type;
			$result = $this->test($func($this->randData->get($type))) && $result;
		}
		
		return $result;
	}
}

new randDataTest();
