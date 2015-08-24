<?php namespace Devtools;

class FileLogger extends Logger
{
    private static $filename;
    private static $formatter;

    public function __construct($filename, Formatter $formatter)
    {
        parent::__construct();
        self::$filename = $filename;
        self::$formatter = $formatter;
    }

    public function write($content, $result = null)
    {
        return self::output($content, $result);
    }

    public static function output($content, $result = null)
    {
        return file_put_contents(
            self::$filename,
            self::$formatter->format($content, $result) . PHP_EOL,
            FILE_APPEND
        );
    }
}
