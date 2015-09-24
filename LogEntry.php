<?php namespace Devtools;

class LogEntry extends ValueObject
{
    protected $required = array('message');
    protected $allowed  = array('result');
}
