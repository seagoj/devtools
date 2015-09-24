<?php namespace Devtools;

class Assertion
{
    public __construct($test, $message)
    {
        $this->test    = $test;
        $this->message = $message;
    }
}
