<?php

class ConfigTest extends PHPUnit_Framework_TestCase {
	private $_log;
	
	public function setup() {
        $options = array('file'=>__CLASS__.'.log');
        
        $this->_log = new \Devtools\Log($options);
    }

    public function testConfig() {
        $config = new \Devtools\Config($this);

	}
}

new configTest();
