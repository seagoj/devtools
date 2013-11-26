<?php

class RandDataTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setup()
    {
        $options = array('type'=>'stdout');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
    }
    /**
     * @covers Devtools\RandData::__construct
     * @covers Devtools\RandData::get
     * @covers Devtools\RandData::randSign
     * @covers Devtools\RandData::randArray
     * @covers Devtools\RandData::randString
     * @covers Devtools\RandData::randInteger
     * @covers Devtools\RandData::randBool
     * @covers Devtools\RandData::randDouble
     **/
    public function testGet()
    {
        $randData = new \Devtools\RandData();
        $types = array('string','array','integer','bool','double');
        foreach ($types AS $type) {
            $func = 'is_'.$type;
            $this->assertTrue($func($randData->get($type)));
        }
    }


    /**
     * @covers Devtools\RandData::get
     *
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    Data of type invalid could not be generated.
     **/
    public function testGetInvalid()
    {
        $randData = new \Devtools\RandData();
        $randData->get('invalid');
    }
}
