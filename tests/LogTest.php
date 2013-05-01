<?php

class LogTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function testLog()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf('\Devtools\Log', $log);
    }

    public function test_config()
    {
        $options = array('file'=>'test_config.log');
        $log = new \Devtools\Log($options);

        $this->assertAttributeEquals(
            array('type'=>'file', 'file'=>'test_config.log'),
            '_config',
            $log
        );
    }

    public function testWrongType()
    {
        $options = array('type'=>'invalid');
        $this->setExpectedException('InvalidArgumentException');
        $log = new \Devtools\Log($options);
    }

    public function testFile()
    {
        $options = array('file'=>'tests/fileTest.log');

        $log = new \Devtools\Log($options);
        $log->file('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options['file'])!=='');
    }

    public function test_tapify()
    {
         
    }
}

