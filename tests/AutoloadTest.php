<?php

class AutoloadTest extends PHPUnit_Framework_TestCase
{
    public function testAutoload()
    {
        require_once 'autoloader.php';
        $this->assertTrue(false);
    }
}
