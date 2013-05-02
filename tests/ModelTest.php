<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
    private $_log;
    private $_logFile;

    public function setUp()
    {
        $this->_logFile = __CLASS__.".log";
        $options = array('file'=>$this->_logFile);
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        $this->unlink($this->_logFile);
    }

    public function testMarkdown()
    {

    }
}
