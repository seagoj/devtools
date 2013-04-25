<?php

class HookTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    public function tearDown()
    {
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
