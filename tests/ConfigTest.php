<?php

class ConfigTest extends PHPUnit_Framework_TestCase {
	private $log;
	
	public function setup() {
        $options = array('file'=>'ConfigTest.log');
        $this->log($options);
    }

    public function testConfig() {}
        $config = new \Devtools\Config($this);

	}
}

new configTest();
