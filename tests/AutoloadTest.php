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

        $last = sizeof($autoloadStack)-1;
        $this->assertEquals($this->validClass, get_class($autoloadStack[$last][0]));
        $this->assertEquals($this->validMethod, $autoloadStack[$last][1]);
    }

    /**
     * @covers Devtools\Autoload::register
     **/
    public function testRegisterPrepend()
    {
        \Devtools\Autoload::register(array('prepend'=>true));
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

        $this->reflectEquals('autoload', 1, 'Devtools\RandData');
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

        $_runPath = '/usr/bin/phpunit';
        $_libPath = '/home/code/Devtools';
        $this->assertEquals(
            "../../../home/code/Devtools/",
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
        $this->reflectEquals(
            'getPath',
            '/home/travis/build/seagoj',
            '/home/travis/build/seagoj/testFile.php'
        );
    }

    /**
     * @param string $method
     * @param string $param
     */
    private function reflectEquals($method, $expected, $param)
    {
        $method = new ReflectionMethod('Devtools\Autoload', $method);
        $method->setAccessible(true);

        $this->assertEquals(
            $expected,
            $method->invoke(new \Devtools\Autoload(), $param)
        );
    }
}
