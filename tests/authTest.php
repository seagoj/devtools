<?php

class HookTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_REQUEST['payload'] = file_get_contents('tests/payload.json');
        $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';
    }

    public function tearDown()
    {
        unset($_REQUEST);
    }

    public function authTest()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Auth', $auth);
    }

    public function validateTest()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
