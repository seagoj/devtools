<?php namespace Devtools;

class StdOutLogger extends Logger
{
    private static $formatter;

    public function __construct(Formatter $formatter)
    {
        self::$formatter = $formatter;
        parent::__construct();
    }

    /* public function write($content, $result = null) */
    /* { */
    /*     self::output($content, $result); */
    /* } */

    public function write($content, $result = null)
    {
        var_dump(__METHOD__.": {$content}");
        echo self::$formatter->format($content, $result) . PHP_EOL;
    }
}
