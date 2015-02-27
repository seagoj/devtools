<?php namespace Devtools;

class TAPFormatter extends Formatter
{
    private $testCount;
    private $first;
    private $timestamp;

    public function __construct()
    {
        $this->textCount = 0;
        $this->first = true;
    }

    public function header()
    {
        date_default_timezone_set('America/Chicago');
        $this->timestamp = date("m-d-Y H:i:s");
        $content = $this->timestamp . PHP_EOL . $content;
        $this->first = false;
        return $content;
    }

    public function footer()
    {
        return "1..{$this->testCount}" . PHP_EOL;
    }

    public function format($content, $result)
    {
        $content = $this->stringify($content);
        $nextTest = $this->testCount + 1;
        $prefix = 'ok ' . $nextTest . ' - ';

        if (!is_null($result)) {
            $this->testCount = $nextTest;
            $content = $prefix . $content;
            if (!$result) {
                $content = 'not ' . $content;
            }
        }

        if ($this->first) {
            $content = $this->header($content);
        }

        return $content;
    }

    public function stringify($content)
    {
        return (is_array($content) || is_object($content)) ?
            serialize($content) :
            $content;
    }
}
