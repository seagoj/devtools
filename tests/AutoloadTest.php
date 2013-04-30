<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function AutoloadTest()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf('Log', $log);
    }
}
