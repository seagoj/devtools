<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    private $registerValidClass;
    private $registerValidMethod;

    public function setup()
    {
        $this->registerValidClass = "Devtools\Autoload";
        $this->registerValidMethod = "_autoload";
    }

    public function testRegisterAppend()
    {
        \Devtools\Autoload::register();
        $autoloadStack = spl_autoload_functions();

        $this->assertEqual($this->registerValidClass, get_class($autoloadStack[9][0]));
        $this->assertEqual($this->registerValidMethod, $autoloadStack[9][1]);
    }

    public function testRegisterPrepend()
    {
        \Devtools\Autoload::register(true);
        $autoloadStack = spl_autoload_functions();
        
        $this->assertEqual($this->registerValidClass, get_class($autoloadStack[0][0]));
        $this->aseertEqual($this->registerValidMethod, $autoloadStack[0][1]);
    }

    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }
}
