<?php

// Required to test output

/**
 * @covers \Devtools\Log()
 **/
class LogTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function tearDown()
    {
        if(is_file('Log.log')) {
            unlink('Log.log');
        }
    }
    
    /**
     * @covers Devtools\Log::__construct
     **/
    public function testInstanceOf()
    {
        $this->assertInstanceOf('Devtools\Log', new \Devtools\Log());
    }

    /**
     * @covers Devtools\Log::__construct
     **/
    public function testDefaults()
    {
        $log = new \Devtools\Log();
        $this->assertAttributeEquals(
            [
                'type' => 'stdout',
                'file' => 'Log.log',
                'format' => 'tap'
            ],
            'config',
            $log
        );
    }

    /**
     * @covers Devtools\Log::__construct
     **/
    public function testCustomTypeValid()
    {
        $options = [
            'type' => 'html'
        ];

        $log = new \Devtools\Log($options);
    }

    /**
     * @covers Devtools\Log::__construct
     **/
    public function testCustomFileValid()
    {
        $options = [
            'type' => 'file',
            'file' => __METHOD__.'.log'
        ];

        $log = new \Devtools\Log($options);

        $this->assertAttributeEquals(
            [
                'type' => 'file',
                'file' => __METHOD__.'.log',
                'format' => 'tap'
            ],
            'config',
            $log
        );
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     **/
    public function testWrongType()
    {
        $options = [
            'type'=>'invalid'
        ];

        $this->setExpectedException('InvalidArgumentException');
        $log = new \Devtools\Log($options);
        $log->write("Brokwn");
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     * @covers Devtools\Log::file
     **/
    public function testFile()
    {
        $options = [
            'type' => 'file',
            'file' => __METHOD__.'.log'
        ];

        $log = new \Devtools\Log($options);
        $log->write('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options['file'])!=='');
        unlink(__METHOD__.'.log');
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     * @covers Devtools\Log::tapify
     **/
    public function testTapifyTrue()
    {
        $message = "Sample Output";

        $method = new ReflectionMethod('Devtools\Log', 'tapify');
        $method->setAccessible(true);

        $this->assertTrue(
            strpos(
                $method->invoke(
                    new \Devtools\Log(), 
                    $message, 
                    true
                ),
                "ok 1 - $message"
            ) !== false
        );
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     * @covers Devtools\Log::tapify
     * @covers Devtools\Log::stdout
     **/
    public function testStdout()
    {
        $message = __METHOD__;
        $options = [
            'type'=>'stdout'
        ];

        $log = new \Devtools\Log($options);

        ob_start();
        $log->write($message);
        $this->assertEquals(
            $message.PHP_EOL,
            $this->stripHeader()
        );
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     * @covers Devtools\Log::htmlify
     **/
    public function testHtml()
    {
        $message = __METHOD__;
        $options = [
            'format' => 'html',
            'type' => 'stdout'
        ];

        $log = new \Devtools\Log($options);

        ob_start();
        $log->write($message);
        $this->assertEquals("<div>$message</div>".PHP_EOL, ob_get_clean());
        
    }

    private function stripHeader()
    {
        return substr(
            $buffer = ob_get_clean(),
            strpos(
                $buffer,
                PHP_EOL
            )+strlen(PHP_EOL)
        );
    }
}
