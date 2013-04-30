<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        require_once 'tests/autoloader.php';
        $this->assertTrue(false);
    }
}
