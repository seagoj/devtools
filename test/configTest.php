<?php
namespace seagoj\devtools; 

require_once("../lib/autoload/src/autoload.php");

class configTest extends unit{
	private $config;
	private $dbg;
	
	public function __construct() {
		$this->config = new config($this);
		$this->dbg = new dbg($this);
		if($this->config->debug)
			$this->dbg->dump("test");
	}
	/*
	public function dump($var) {
		$this->dbg->dump($var);
	}*/
}

new configTest();