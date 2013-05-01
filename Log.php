<?php
namespace Devtools;
    
class Log {
    private $testCount;
    private $_config;
    private $_file;

    public function __construct($options)
    {
        $this->_config($options);

        switch($this->_config['type']) {
            case 'file':    
                $this->_file = $file;
                $this->_file(date("m-d-Y H:i:s"));
                $this->_testCount = 0;
                break;
            default:
                throw new Exception($this->_config['type'].' is not a valid Log type');
                break;
        }
    }

    public function file($content, $result='EMPTY')
    {
        $endline = "\r\n";
        $content = $this->_tapify($content, $result);

        print "<div>$content</div>";
        return file_put_contents($this->_file, $content.$endline, FILE_APPEND);
    }

    private function _config($options)
    {
        $defaults = [
            'type'=>'file',
            'file'=>'Log.log'
        ];

        foreach($defaults as $default=>$value) {
            if(in_array($default, $options)) {
                $defaults[$default] = $options[$default];
            }
        }

        $this->_config = $defaults;
        
    }
    private function _tapify($content, $result) {
        $nextTest = $this->testCount+1;
        $prefix = 'ok '.$nextTest.' - ';
            
        if($result!=='EMPTY') {
                $this->testCount = $nextTest;
                $content = $prefix.$content;
            if(!$result) {
                $content = 'not '.$content;
            }
        }

        return $content;
    }
        
    public function __destruct()
    {
        $postfix = '1..'.$this->testCount;
        $this->file($postfix);
        $this->file("\r\n");
    }
}
