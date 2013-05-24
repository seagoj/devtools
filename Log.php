<?php
namespace Devtools;

class Log
{
    private $_testCount;
    private $_config;
    private $_headers;
    private $_footers;
    public $type;

    public function __construct($options=[])
    {
        $defaults = [
            'type'=>'file',
            'file'=>'Log.log',
            'format'=>'tap'
        ];

        $headers = array(
            'tap'=>date("m-d-Y H:i:s")
        );

        $this->_config = array_merge($defaults, $options);
        $this->type = $this->_config['type'];
        $this->write($headers[$this->_config['format']]);
        $this->_testCount = 0;
    }

    public function write($content, $result='EMPTY')
    {
        $content = $this->_stringify($content);

        switch ($this->_config['format']) {
            case 'tap':
                $content = $this->_tapify($content, $result);
                break;
            default:
                throw new \InvalidArgumentException($this->_config['format'].' is not a valid log format.');
                break;
        }

        switch ($this->_config['type']) {
            case 'file':
                $this->_file($content);
                break;
            case 'html':
                $this->_html($content);
                break;
            case 'stdout':
                $this->_stdout($content);
                break;
            default:
                throw new \InvalidArgumentException($this->_config['type'].' is not a valid Log type');
                break;
        }
    }

    private function _file($content)
    {
        $endline = "\r\n";

        return file_put_contents($this->_config['file'], $content.$endline, FILE_APPEND);
    }

    private function _html($content)
    {
        $tag = 'div';
        print "<$tag>$content</$tag>";
    }

    private function _stdout($content)
    {
        print $content."\n";
    }

    private function _stringify($content)
    {
        if(is_array($content))

            return serialize($content);
        else
            return $content;
    }

    private function _tapify($content, $result)
    {
        $nextTest = $this->_testCount+1;
        $prefix = 'ok '.$nextTest.' - ';

        if ($result!=='EMPTY') {
                $this->_testCount = $nextTest;
                $content = $prefix.$content;
            if (!$result) {
                $content = 'not '.$content;
            }
        }

        return $content;
    }

    public function __destruct()
    {
        $start = $this->_testCount===0 ? 0 : 1;
        $footers = array(
            'tap'=>$start.'..'.$this->_testCount."\r\n"
        );

        $this->write($footers[$this->_config['format']]);
    }
}
