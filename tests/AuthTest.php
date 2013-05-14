<?php

class AuthTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setUp()
    {
        print "<div>Begin ".__CLASS__;
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        $this->unlink(__CLASS__.'.log');
        print "End ".__CLASS__."</div>";
    }

    public function testAuth()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Devtools\Auth', $auth);
        $this->_log->write('$auth is instance of Auth','EMPTY','authTest.log');
    }

    public function testValidate()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
