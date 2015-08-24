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

    public static function write($content, $result = null)
    {
        echo self::$formatter->format($content, $result) . PHP_EOL;
    }
}
