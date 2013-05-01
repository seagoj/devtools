<?php

class AuthTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {i
        $options = array('file'=>'AuthTest.log');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        $this->unlink('AuthTest.log');
    }

    public function testAuth()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Devtools\Auth', $auth);
        $this->log->file('$auth is instance of Auth','EMPTY','authTest.log');
    }

    public function testValidate()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
