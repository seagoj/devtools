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
		$types = array('string','array','integer','bool','double','null');
		$result = true;
		foreach($types AS $type) {
			$func = 'is_'.$type;
			$this->sssertTrue($func($this->randData->get($type)));
		}
    }

    public function tearDown()
    {
        unlink(__CLASS__.'.log');
    }
}

