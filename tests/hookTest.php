<?php

class HookTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $_REQUEST['payload'] = file_get_contents('tests/payload.json');
        $_SERVER['HTTP_CLIENT_IP'] = '127.0.0.1';
        if(!is_dir('tests/docroot')) mkdir('tests/docroot');
        if(!is_dir('tests/docroot/hook')) mkdir('tests/docroot/hook');
    }

    public function tearDown()
    {
        unset($_REQUEST);
        unset($_SERVER['HTTP_CLIENT_IP']);
    }

    public function testPayload()
    {
        $this->assertTrue(isset($_REQUEST['payload']));
    }
    
    public function testHook()
    {
        $options = ['docroot'=>'tests/docroot/'];
        $goodResult = "Already up-to-date";

        $hook = new Hook($options);
        $this->assertInstanceOf('Hook', $hook);
        $this->assertTrue(strpos($hook->output, $goodResult)!== false);
    }
}

