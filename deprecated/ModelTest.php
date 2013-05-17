<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
    private $_log;
    private $_logFile;

    public function setUp()
    {
        $options = array('type'=>'stdout');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
    }

    public function testMarkdown()
    {

    }
}
