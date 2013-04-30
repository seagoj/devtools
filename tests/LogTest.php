<?php

class LogTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
        
    }

    public function tearDown()
    {
        
    }

    public function logTest()
    {
        require "Log.php";
        $log = new \Devtools\Log();
        $this->assertInstanceOf('Log', $log);
    }

    public function fileTest()
    {
        $options = ['file'=>'tests/fileTest.log'];

        $log = new \Devtools\Log($options);
        $log->file('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options)!=='');
    }
}

