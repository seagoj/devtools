<?php
namespace Devtools;

class Log
{
    private $testCount;
    private $config;
    public $type;

    public function __construct($options = [])
    {
        $defaults = [
            'type'=>'file',
            'file'=>'Log.log',
            'format'=>'tap'
        ];

        $headers = array(
            'tap'=>date("m-d-Y H:i:s")
        );

        $this->config = array_merge($defaults, $options);
        $this->type = $this->config['type'];
        $this->write($headers[$this->config['format']]);
        $this->testCount = 0;
    }

    public function write($content, $result = 'EMPTY')
    {
        $content = $this->stringify($content);

        switch ($this->config['format']) {
            case 'tap':
                $content = $this->tapify($content, $result);
                break;
            default:
                throw new \InvalidArgumentException($this->config['format'].' is not a valid log format.');
                break;
        }

        switch ($this->config['type']) {
            case 'file':
                $this->file($content);
                break;
            case 'html':
                $this->html($content);
                break;
            case 'stdout':
                $this->stdout($content);
                break;
            default:
                throw new \InvalidArgumentException($this->config['type'].' is not a valid Log type');
                break;
        }
    }

    private function file($content)
    {
        $endline = "\r\n";

        return file_put_contents($this->config['file'], $content.$endline, FILE_APPEND);
    }

    private function html($content)
    {
        $tag = 'div';
        print "<$tag>$content</$tag>";
    }

    private function stdout($content)
    {
        print $content."\n";
    }

    private function stringify($content)
    {
        if (is_array($content)) {
            return serialize($content);
        } else {
            return $content;
        }
    }

    private function tapify($content, $result)
    {
        $nextTest = $this->testCount+1;
        $prefix = 'ok '.$nextTest.' - ';

        if ($result!=='EMPTY') {
                $this->testCount = $nextTest;
                $content = $prefix.$content;
            if (!$result) {
                $content = 'not '.$content;
            }
        }

        return $content;
    }

    public function __destruct()
    {
        $start = $this->testCount===0 ? 0 : 1;
        $footers = array(
            'tap'=>$start.'..'.$this->testCount."\r\n"
        );

        $this->write($footers[$this->config['format']]);
    }
}
