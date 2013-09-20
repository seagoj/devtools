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
     **/
    public function testInvalidFormat()
    {
        $this->setExpectedException('InvalidArgumentException');
        $log = new \Devtools\Log(['format' => 'invalid']);
        $log->write("Brgasjk");
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
            'file' => 'testFile.log'
        ];

        $log = new \Devtools\Log($options);
        $log->write('Test');

        $this->assertTrue(is_file($options['file']));
        $this->assertTrue(file_get_contents($options['file'])!=='');
        unlink('testFile.log');
    }

    /**
     * @covers Devtools\Log::__construct
     * @covers Devtools\Log::write
     * @covers Devtools\Log::stringify
     * @covers Devtools\Log::tapify
     **/
    public function testTapify()
    {
        $message = "Sample Output";
        $log = new \Devtools\Log();

        ob_start();
        $log->write($message);
        $this->assertEquals(
            $message.PHP_EOL,
            $this->stripHeader(ob_get_clean())
        );

        ob_start();
        $log->write($message, true);
        $this->assertEquals("ok 1 - $message".PHP_EOL, ob_get_clean());

        ob_start();
        $log->write($message, false);
        $this->assertEquals("not ok 2 - $message".PHP_EOL, ob_get_clean());
            
        ob_start();
        unset($log);
        ob_get_clean();
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
        unset($log);
        $this->assertEquals(
            $message.PHP_EOL,
            $this->stripHeader(ob_get_clean())
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

        ob_start();
        $log->write($message, true);
        $log->__destruct();
        unset($log);
        $this->assertEquals(
            "<div>".true.": $message</div>".PHP_EOL,
            ob_get_clean()
        );
    }

    private function stripHeader($buffer)
    {

        return substr(
            $buffer,
            strpos(
                $buffer,
                PHP_EOL
            )+strlen(PHP_EOL)
        );
    }
}
