<?php

// Required to test output
require_once 'PHPUNIT/Extensions/OutputTestCase.php';

class LogTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function testInstanceOf()
    {
        $this->assertInstanceOf('Devtools\Log', new \Devtools\Log());
    }

    public function testDefaults()
    {
        $log = new \Devtools\Log();
        $this->assertAttributeEquals(
            array('type'=>'file', 'file'=>'Log.log', 'format'=>'tap'),
            '_config',
            $log
        );
        $this->assertTrue(is_file('Log.php'));
        $this->assertTrue(file_get_contents('Log.php')!=='');
    }

    public function testCustomTypeValid()
    {
        $options = array('type'=>'html');
        $log = new \Devtools\Log($options);       
    }

    public function testCustomFileValid()
    {
        $options = array('file'=>__METHOD__.'.log');
        $log = new \Devtools\Log($options);

        $this->assertAttributeEquals(
            array('type'=>'file', 'file'=>__METHOD__.'.log', 'format'=>'tap'),
            '_config',
            $log
        );
    }

    public function testWrongType()
    {
        $options = array('type'=>'invalid');
        $this->setExpectedException('InvalidArgumentException');
        $log = new \Devtools\Log($options);
    }

    public function testFile()
    {
        $options = array('file'=>__METHOD__.'.log');

        $log = new \Devtools\Log($options);
        $log->write('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options['file'])!=='');
    }

    public function test_tapifyTrue()
    {
        $message = "Sample Output";

        $method = new ReflectionMethod('Devtools\Log', '_tapify');
        $method->setAccessible(true);

        
        $this->assertEquals("ok 1 - $message", $method->invoke(new \Devtools\Log(), $message, true));
    }

    public function test_stdout()
    {
        $message = __METHOD__;
        $this->expectedOutputString($message."\n");
        )
        $options = array('type'=>'stdout');
        $log = new \Devtools\Log($options);

        $log->write($message);
    }
}
