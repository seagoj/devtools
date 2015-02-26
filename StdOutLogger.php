<?php namespace Devtools;

class StdOutLogger extends Logger
{
    public function __construct(Formatter $formatter)
    {
        $this->formatter = $formatter;
        parent::__construct();
    }

    public function __destruct()
    {
        /* echo $this->formatter->footer(); */
    }

    public function write($content, $result = null)
    {
        echo $this->formatter->format($content, $result) . PHP_EOL;
    }
}