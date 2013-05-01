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

        $this->assertEquals($this->registerValidClass, get_class($autoloadStack[9][0]));
        $this->assertEquals($this->registerValidMethod, $autoloadStack[9][1]);
    }

    public function testRegisterPrepend()
    {
        \Devtools\Autoload::register(true);
        $autoloadStack = spl_autoload_functions();
        
        $this->assertEquals($this->registerValidClass, get_class($autoloadStack[0][0]));
        $this->assertEquals($this->registerValidMethod, $autoloadStack[0][1]);
    }

    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
        $this->assertTrue(true);
    }

    public function testCheckEnv()
    {
        $_SERVER['SCRIPT_FILENAME'] = '/home/travis/build/seagoj/php/bin/phpunit';
        $autoload = new \Devtools\Autoload();
        $this->assertEquals($autoload->checkEnv(), 'PHPUNIT_TRAVIS');

        $_SERVER['SCRIPT_FILENAME'] = 'home/travis/build/seagoj';
        $this->assertEquals($autoload->checkEnv(), '');
    }

    public function test_getRelPath()
    {
        $method = new ReflectionMethod('Devtools\Autoload', '_getRelPath');

        $method->setAccessible(true);

        var_dump($method->invoke(new devtools\Autoload));
    }
}
