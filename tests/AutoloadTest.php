<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        \Devtools\Autoload::register();
        var_dump(spl_autoload_functions());
    }

    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }
}
