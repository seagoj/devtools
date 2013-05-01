<?php

class HookTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        $this->log = new \Devtools\Log();
    }

    public function tearDown()
    {
    }

    public function testAuth()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Auth', $auth);
        $this->log->file('$auth is instance of Auth','EMPTY','authTest.log');
    }

    public function testValidate()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
