<?php namespace Devtools;

class StdOutLogger extends Logger
{
    private $formatter;

    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
        parent::__construct();
    }

    public function write($content, $result = null)
    {
        echo $this->formatter->format($content, $result) . PHP_EOL;
    }
}
