<?php

class Log {
    private $type;
    private $path;
    private $testCount;
    
    public function __construct($location, $syntax='tap')
    {
        if($location=='console' || $location=='html' || $location=='sreen') {
            $this->type = $location    
        } else {
            $this->type = 'file';
            $this->path = $location;
        }
    }

    public function log($content, $test='EMPTY') {
        $endline = "\r\n";
        switch($this->syntax) {
            case 'tap':
                $content = $this->tapify($content, $test)
                break;
            deault:
                die("$this->type is not a valid syntax type");
                break;
        };
        switch($this->syntax)

    }

    private function tapify($content, $test) {
        $nextTest = $this->testCount+1;
        $prefix = 'ok '.$nextTest.' - ';
            
        if($test!=='EMPTY') {
            $this->testCount = $nextTest;
            $content = $prefix.$content;
            if(!$test) {
                $content = 'not '.$content;
            }
        }
        return $content;
    }
}
