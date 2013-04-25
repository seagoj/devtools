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
        $log = new Log();
        $this->assertInstanceOf('Log', $log);
    }

    public function logToFileTest()
    {
            
    }
}
