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
        $log = \Devtools\Log(array('file'=>'test_config.log'));
        $this->assertAttributeEquals(
            array('type'=>'file', 'file'=>'test_config.log'),
            '_config',
            $log
        );
    }

    public function testFile()
    {
        $options = array('file'=>'tests/fileTest.log');

        $log = new \Devtools\Log($options);
        $log->file('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options)!=='');
    }

    public function test_tapify()
    {
         
    }
}

