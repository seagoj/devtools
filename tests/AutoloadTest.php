<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    private $validClass;
    private $validMethod;

    public function setup()
    {
        $this->validClass = "Devtools\Autoload";
        $this->validMethod = "autoload";
    }

    public function tearDown()
    {
    }

    /**
     * @covers Devtools\Autoload::register
     **/
    public function testRegisterAppend()
    {
        \Devtools\Autoload::register();
        $autoloadStack = spl_autoload_functions();

        $this->assertEquals($this->validClass, get_class($autoloadStack[9][0]));
        $this->assertEquals($this->validMethod, $autoloadStack[9][1]);
    }

    /**
     * @covers Devtools\Autoload::register
     **/
    public function testRegisterPrepend()
    {
        \Devtools\Autoload::register(true);
        $autoloadStack = spl_autoload_functions();

        $this->assertEquals($this->validClass, get_class($autoloadStack[0][0]));
        $this->assertEquals($this->validMethod, $autoloadStack[0][1]);
    }

    /**
     * @covers Devtools\Autoload::__construct
     * @covers Devtools\Autoload::checkEnv
     * @covers Devtools\Autoload::getRelPath
     * @covers Devtools\Autoload::getPath
     * @covers Devtools\Autoload::autoload
     **/
    public function testAutoload()
    {
        $log = new \Devtools\Log();
        $this->assertInstanceOf("\Devtools\Log", $log);

        $method = new ReflectionMethod('Devtools\Autoload', 'autoload');
        $method->setAccessible(true);

        $this->assertEquals(
            $method->invoke(new \Devtools\Autoload(), 'Devtools\RandData'),
            1
        );

        
    }

    /**
     * @covers Devtools\Autoload::__construct
     * @covers Devtools\Autoload::checkEnv
     **/
    public function testCheckEnv()
    {
        $_SERVER['SCRIPT_FILENAME'] =
            '/home/travis/build/seagoj/php/bin/phpunit';

        $autoload = new \Devtools\Autoload();
        $this->assertEquals($autoload->checkEnv(), 'PHPUNIT');

        $_SERVER['SCRIPT_FILENAME'] = 'home/travis/build/seagoj';
        $this->assertEquals($autoload->checkEnv(), '');
    }

    /**
     * @covers Devtools\Autoload::checkEnv
     * @covers Devtools\Autoload::__construct
     * @covers Devtools\Autoload::getRelPath
     **/
    public function testGetRelPath()
    {
        $method = new ReflectionMethod('Devtools\Autoload', 'getRelPath');
        $method->setAccessible(true);

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath.'/lib/lib';
        $this->assertEquals(
            'lib/lib/', 
            $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath)
        );

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath.'/lib';
        $this->assertEquals(
            'lib/', 
            $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath)
        );

        $_runPath = '/home/travis/build/seagoj/tests';
        $_libPath = '/home/travis/build/seagoj/lib';
        $this->assertEquals(
            '../lib/', 
            $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath)
        );

        $_runPath = '/home/travis/build/seagoj';
        $_libPath = $_runPath;
        $this->assertEquals(
            '', 
            $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath)
        );

        $_runPath = '/home/travis/build/seagoj/tests/src';
        $_libPath = '/home/travis/build/seagoj/lib';
        $this->assertEquals(
            '../../lib/', 
            $method->invoke(new \Devtools\Autoload(), $_runPath, $_libPath)
        );
    }

    /**
     * @covers Devtools\Autoload::checkEnv
     * @covers Devtools\Autoload::__construct
     * @covers Devtools\Autoload::getPath
     **/
    public function testGetPath()
    {
        $method = new ReflectionMethod('Devtools\Autoload', 'getPath');
        $method->setAccessible(true);

        $file = '/home/travis/build/seagoj/testFile.php';
        $this->assertEquals(
            '/home/travis/build/seagoj', 
            $method->invoke(new \Devtools\Autoload(), $file)
        );
    }
}
