<?php

class MarkdownTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setUp()
    {
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        $this->unlink(__CLASS.'.log');
    }

    public function testMarkdown()
    {

    }
}
