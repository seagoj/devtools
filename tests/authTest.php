<?php

class HookTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        require "Log.php";
        $this->log = new Log();
    }

    public function tearDown()
    {
    }

    public function authTest()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Auth', $auth);
        $this->log->logToFile('$auth is instance of Auth','EMPTY','authTest.log');
    }

    public function validateTest()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
