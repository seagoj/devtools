<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    private $_log;
    private $_validClass;
    private $_validMethod;

    public function setup()
    {
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
        $this->_validClass = "Devtools\Autoload";
        $this->_validMethod = "_autoload";
    }

    public function testRegisterAppend()
    {
        \Devtools\Autoload::register();
        $autoloadStack = spl_autoload_functions();

        $this->assertEquals($this->_validClass, get_class($autoloadStack[9][0]));
        $this->assertEquals($this->_validMethod, $autoloadStack[9][1]);
    }

    public function testRegisterPrepend()
    {
        \Devtools\Autoload::register(true);
        $autoloadStack = spl_autoload_functions();
        
        $this->assertEquals($this->_validClass, get_class($autoloadStack[0][0]));
        $this->assertEquals($this->_validMethod, $autoloadStack[0][1]);
    }

    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);
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
        

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath.'/lib/lib';
        $this->assertEquals('lib/lib/', $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath));

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath.'/lib';
        $this->assertEquals('lib/', $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath));

        $_runPath = '/home/travis/build/seagoj/tests';
        $_libPath = '/home/travis/build/seagoj/lib';
        $this->assertEquals('../lib/', $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath));

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath;
        $this->assertEquals('', $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath));

        $_runPath = '/home/travis/build/seagoj/tests/src';
        $_libPath = '/home/travis/build/seagoj/lib';
        $this->assertEquals('../../lib/', $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath));
    }

    public function test_getPath()
    {
        $method = new ReflectionMethod('Devtools\Autoload', '_getPath');
        $method->setAccessible(true);

        $file = '/home/travis/build/seagoj/testFile.php';
        $this->assertEquals('/home/travis/build/seagoj', $method->invoke(new \Devtools\Autoload(), $file));
    }

    public function tearDown()
    {
        // unlink(__CLASS__.'.log');')
    }
}
