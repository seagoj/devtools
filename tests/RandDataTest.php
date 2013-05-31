<?php

class RandDataTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setup()
    {
        $options = array('type'=>'stdout');
        $this->log = new \Devtools\Log($options);
    }

    public function testGet()
    {
        $randData = new \Devtools\RandData();
        $types = array('string','array','integer','bool','double');
        foreach ($types AS $type) {
            $func = 'is_'.$type;
            $this->assertTrue($func($randData->get($type)));
        }
    }

    public function tearDown()
    {
    }
}
