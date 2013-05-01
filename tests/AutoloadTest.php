<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        \Devtools\Autoload::register();
        $autoloadStack = spl_autoload_functions();
        $validClass = "class Devtools\Autoload#";
        $validMethod = "_autoload";

        var_dump(get_class($autoloadStack[9][0]));

//        foreach ($autoloadStack[9][0] as $class=>$parameters) {
        $class = array_keys($autoloadStack[9][0]);
            $this->assertEqual($validClass, substr($class, 0, strlen($validClass)-1));
//        }
    }

    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }
}
