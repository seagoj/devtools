<?php
    
namespace Devtools;
    
class Log {
    private $testCount;

    public function __construct($file='Log.log')
    {
        $this->file = $file;
        $this->logToFile(date("m-d-Y H:i:s"));
        $this->testCount = 0;
    }

    public function file($content, $result='EMPTY')
    {
        $endline = "\r\n";
        $content = $this->tapify($content, $result);

        print "<div>$content</div>";
        return file_put_contents($this->file, $content.$endline, FILE_APPEND);
    }

    private function tapify($content, $result) {
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
        $this->logToFile($postfix);
        $this->logToFile("\r\n");
    }
}
