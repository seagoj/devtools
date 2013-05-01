<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }
}
