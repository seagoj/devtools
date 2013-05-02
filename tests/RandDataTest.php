<?php

class RandDataTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setup()
    {
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
    }

    public function testGet()
    {
		$randData = new \Devtools\RandData();
        $types = array('string','array','integer','bool','double','null');
		foreach($types AS $type) {
			$func = 'is_'.$type;
			$this->assertTrue($func($randData->get($type)));
		}
    }

    public function tearDown()
    {
        unlink(__CLASS__.'.log');
    }
}

