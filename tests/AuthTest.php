<?php

class AuthTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        $options = array('type' => 'stdout');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
    }

    /**
     * @covers Devtools\Auth::__construct
     * @covers Devtools\Auth::hash
     * @covers Devtools\Auth::validate
     **/
    public function testAuth()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertInstanceOf('Devtools\Auth', $auth);
    }

    /**
     * @covers Devtools\Auth::__construct
     * @covers Devtools\Auth::hash
     * @covers Devtools\Auth::validate
     **/
    public function testValidate()
    {
        $auth = new \Devtools\Auth("user", "password");
        $this->assertTrue($auth->validate("user", "password"));
        $this->assertFalse($auth->validate("user", "not password"));
    }
}
