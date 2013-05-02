<?php

class GitTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setUp()
    {
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
        unlink(__CLASS__.'.log');
    }

    public function testGit()
    {
        $options = array(
            'user'=>'seagoj'
        );
        $git = new \Devtools\Git($options);
    }
}
