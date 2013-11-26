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
     * @covers Devtools\Auth
     * @covers Devtools\Auth::__construct
     * @covers Devtools\Auth::hash
     * @covers Devtools\Auth::validate
     **/
    public function testAuth()
    {
        $this->assertInstanceOf(
            'Devtools\Auth',
            new \Devtools\Auth("user", "password")
        );

        $this->assertInstanceOf(
            'Devtools\Auth',
            new \Devtools\Auth()
        );

        $this->assertInstanceOf(
            'Devtools\Auth',
            new \Devtools\Auth()
        );
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

    /**
     * @covers Devtools\Auth::hash
     **/
    public function testHash()
    {
        $auth = new \Devtools\Auth("user", "password");

        $this->assertTrue(!is_null($auth->hash("password")));
    }
}
