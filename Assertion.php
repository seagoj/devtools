<?php namespace Devtools;

class Assertion
{
    public function __construct($test, $message)
    {
        $this->test    = $test;
        $this->message = $message;
    }
}
