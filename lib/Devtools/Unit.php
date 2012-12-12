<?php
namespace seagoj\devtools;
require_once('../lib/autoload/src/autoload.php');

class unit extends dbg {
    
    protected $commentTags;
    private $testObj;
    private $unitObj;
    private $methods;
    private $passed;
    private $depends;
    private $config;
    private $dbg;
    
    public function __construct() {
    }
    
    public function runTest() {
        if(!isset($this->testObj) || !isset($this->unitObj)) die("Test object and unit object are not properly defined.");
        if($this->config->debug) {
            print "<style>.errLabel{color:blue;}.errDesc{color:black;}.function{text-align:center;border:2px solid;border-radius:5px;-moz-border-radius:25px; /* Firefox 3.6 and earlier */}</style><div>Testing ".get_class($this->testObj)."</div>";
            $this->commentTags = array();
            $this->dbg->test(is_object($this->testObj),get_class($this->testObj)." is not an object.");
            $this->dbg->test(is_object($this->unitObj),get_class($this->unitObj)." is not an object.");
            $this->methods = get_class_methods($this->testObj);
            $this->passed = array();
            if(sizeof($this->methods)>0) {
                foreach($this->methods AS $method) {
                    if(!in_array($method,$this->passed)) {
                        if(sizeof($this->depends)>0) {
                            if(in_array($method,$this->depends))
                            {
                                foreach($this->depends[$method] AS $dependancy) {
                                    if(!in_array($dependancy,$this->passed)) {
                                        print "<div class='function'>";
                                        $result = $this->unitObj->$dependsTest();
                                        $result ? print "<div>".$dependsTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($dependsTest."() failed.");
                                    }
                                }
                            }
                        }
                        $methodTest = $method."Test";
                        print "<div class='function'>";
                        $result = $this->unitObj->$methodTest();
                        $result ? print "<div>".$methodTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($methodTest."() failed.");
                    }
                    array_push($this->passed, $method);
                }
                $this->results();
            }
        }
    }
    
    public function results() {
        $pass=NULL;
        if(sizeof($this->methods)==1) {
            $method = $this->methods[0];
            $pass = dbg::test(!in_array($method."Test",$this->passed),"$method did not pass Unit tests.");
        } else {
            foreach($this->methods AS $method) {
                if($pass==NULL)
                    $pass = dbg::test(!in_array($method."Test",$this->passed),"$method did not pass Unit tests.");
                else
                    $pass = $pass && dbg::test(!in_array($method."Test",$this->passed),"$method did not pass Unit tests.");
            }
        }
            
        $pass ? print "<div>".get_class($this->testObj)." passed all unit tests</div><div>&nbsp;</div>": "";
    }
    
    public function __destruct() {}
    
    protected function testing($obj) {
        $this->testObj = $obj;
        $this->config = new config($this->testObj);
        $this->dbg = new dbg($this->testObj);
    }
    protected function with($unitObj) {
        $this->unitObj = $unitObj;
    }
    private function buildDepends() {
        $unitTags = $this->getUnitTags($_SERVER['SCRIPT_FILENAME']);
        foreach($unitTags AS $method=>$comments) {
            $temp = array();
            foreach($comments AS $comment) {
                foreach($comment AS $tag=>$value) {
                    if($tag=='@depends') {
                        array_push($temp,$value);
                    }
                }
            }
            $this->depends[$method]=$temp;
        }
        return true;
    }
    public function getCommentTags($filename,$type=''){
        $this->scanCommentsForTags($filename,$type);
        return $this->commentTags;
    }
    public function getUnitTags($filename) {
        $this->scanCommentsForTags($filename);
        $unitTags = array(
                '@expectedException',
                '@expectedExceptionCode',
                '@expectedExceptionMessage',
                '@dataProvider',
                '@depends',
        );
        $return = array();
        foreach($this->commentTags AS $method=>$values) {
            foreach($values AS $value) {
                foreach($value AS $tag=>$comment) {
                    if(in_array($tag,$unitTags)){
                        $return = array($method=>array(array("$tag"=>"$comment")));
                    }
                }
            }
        }
        return $return;
    }
    private function scanCommentsForTags($filename='dbg.php', $type='') {
        $source = file_get_contents($filename);
    
        $tokens = token_get_all($source);
        $comment = array(
                T_COMMENT,      // All comments since PHP5
                T_DOC_COMMENT   // PHPDoc comments
        );
        $commentTags = array();
        $fuctionTags = array();
        $commentsFound = false;
        $functionFound = false;
        $whitespace = array(" "=>0,"\t"=>0,"\r"=>0,"\n"=>0,"\r\n"=>0,"\n\r"=>0);
        foreach( $tokens as $token ) {
                
            if(in_array($token[0],$comment)) {
                preg_match_all('/@[a-zA-Z0-9 \t_]*/',$token[1],$matches);
    
                foreach($matches[0] AS $match) {
                    $firstPos = 0;
                    $rightChar = '';
                    foreach($whitespace AS $char=>$pos) {
                        if($pos = strpos($match,$char)) {
                            //dbg::msg("$char $match ");
                            if($firstPos==0||$pos<$firstPos) {
                                $firstPos = $pos;
                                $rightChar = $char;
                            }
                            $whitespace[$char]=$pos;
                        }
                    }
                    $pivot = $firstPos;
                    
                    dbg::test($pivot!=0, "Pivot is zero");
                    $tag = substr($match,0,$pivot);
                    $value = substr($match,$pivot+1);
                    array_push($commentTags, array($tag=>$value));
                    $commentsFound = true;
                }
            } else if(($token[0]==T_FUNCTION ||$token[0]==T_CLASS)&& $commentsFound) {
                $functionFound = true;
                $commentsFound = false;
            } else if($token[0]==307 && $functionFound) {
                while($tag = array_pop($commentTags)) {
                    $this->commentTags[$token[1]] = array($tag);
                }
                $functionFound = false;
            }
        }
    }
}
