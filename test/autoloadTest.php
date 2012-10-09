<?php
namespace seagoj\devtools;

require_once('../lib/autoload/src/autoload.php');

class autoloadTest extends unit
{
	private $config;
	
	function __construct() {
		//$this->test(is_object($this));
		//$this->unit = new unit(new autoload('dbg'),$this);
		$this->config = new config($this);
		print_r($this->config);
	}
	
	public function __constructTest() {
		dbg::dump("test");
		return true;
	}
}

new autoloadTest();