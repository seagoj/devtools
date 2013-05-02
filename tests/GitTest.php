<?php

class GitTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        $options = array('file'=>'GitTest.log');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        $this->unlink('GitTest.log');
    }

    public function testGit()
    {

    }
}
