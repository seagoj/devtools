<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->InstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }
}
