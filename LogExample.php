<?php namespace Devtools;

class LogExample extends BaseSubject
{
    public function __construct(Logger $log)
    {
        $this->log = $log;
        $this->attach($log);
    }

    public function test()
    {
        $this->fire('log', ['param1' => 'value1', 'param2' => 'value2']);
    }
}
