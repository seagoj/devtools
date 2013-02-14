<?php
/**
 * Unit: Unit testing class for PHP
 * 
 * @name      Unit
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
namespace Devtools;

/**
 * Config: Defines configuration for current project
 * 
 * @name      Config
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
class Unit extends Dbg
{
    protected $commentTags;
    private $_testObj;
    private $_unitObj;
    private $_methods;
    private $_passed;
    private $_depends;
    private $_config;
    private $_dbg;
    
    /**
     * Unit::__construct
     *
     * Constructor for class Unit
     *
     * @return void
     */
    public function __construct()
    {
    }
    
    /**
     * Unit::runTest()
     *
     * Runs the unit test against the class
     *
     * @return void
     */
    public function runTest()
    {
        if (!isset($this->_testObj) || !isset($this->_unitObj)) die("Test object and unit object are not properly defined.");
        if ($this->_config->debug) {
            print "<style>.errLabel{color:blue;}.errDesc{color:black;}.function{text-align:center;border:2px solid;border-radius:5px;-moz-border-radius:25px; /* Firefox 3.6 and earlier */}</style><div>Testing ".get_class($this->_testObj)."</div>";
            $this->commentTags = array();
            $this->_dbg->test(is_object($this->_testObj), get_class($this->_testObj)." is not an object.");
            $this->_dbg->test(is_object($this->_unitObj), get_class($this->_unitObj)." is not an object.");
            $this->_methods = get_class_methods($this->_testObj);
            $this->_passed = array();
            if (sizeof($this->_methods)>0) {
                foreach ($this->_methods AS $method) {
                    if (!in_array($method, $this->_passed)) {
                        if (sizeof($this->_depends)>0) {
                            if (in_array($method, $this->_depends)) {
                                foreach ($this->_depends[$method] AS $dependancy) {
                                    if (!in_array($dependancy, $this->_passed)) {
                                        print "<div class='function'>";
                                        $result = $this->_unitObj->$dependsTest();
                                        $result ? print "<div>".$dependsTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($dependsTest."() failed.");
                                    }
                                }
                            }
                        }
                        $methodTest = $method."Test";
                        print "<div class='function'>";
                        $result = $this->_unitObj->$methodTest();
                        $result ? print "<div>".$methodTest."() passed all unit tests</div></div><div>&nbsp;</div>" : die($methodTest."() failed.");
                    }
                    array_push($this->_passed, $method);
                }
                $this->results();
            }
        }
    }
    
    /**
     * Unit::results()
     *
     * Displays results of Unit testing
     *
     * @return void
     */
    public function results()
    {
        $pass=null;
        if (sizeof($this->_methods)==1) {
            $method = $this->_methods[0];
            $pass = dbg::test(!in_array($method."Test", $this->_passed), "$method did not pass Unit tests.");
        } else {
            foreach ($this->_methods AS $method) {
                if ($pass==null)
                    $pass = dbg::test(!in_array($method."Test", $this->_passed), "$method did not pass Unit tests.");
                else
                    $pass = $pass && dbg::test(!in_array($method."Test", $this->_passed), "$method did not pass Unit tests.");
            }
        }
            
        $pass ? print "<div>".get_class($this->_testObj)." passed all unit tests</div><div>&nbsp;</div>": "";
    }
    
    /**
     * Unit::__destruct
     *
     * Destructor for Unit class
     *
     * @return void
     */
    public function __destruct()
    {
    }
    
    /**
     * Unit::testing
     *
     * Defines obj to test
     *
     * @param object $obj Object to test
     *
     * @return void
     */
    protected function testing($obj)
    {
        $this->_testObj = $obj;
        $this->_config = new config($this->_testObj);
        $this->_dbg = new dbg($this->_testObj);
    }
    
    /**
     * Unit::with()
     *
     * Defines Unit test class
     *
     * @param object $unitObj Object of unit test class
     *
     * @return void
     */
    protected function with($unitObj)
    {
        $this->_unitObj = $unitObj;
    }
    
    /**
     * Unit::_buildDepends
     *
     * Build dependency list for testing
     *
     * @return bool
     */
    private function _buildDepends()
    {
        $unitTags = $this->getUnitTags($_SERVER['SCRIPT_FILENAME']);
        foreach ($unitTags AS $method=>$comments) {
            $temp = array();
            foreach ($comments AS $comment) {
                foreach ($comment AS $tag=>$value) {
                    if ($tag=='@depends') {
                        array_push($temp, $value);
                    }
                }
            }
            $this->_depends[$method]=$temp;
        }
        return true;
    }
    
    /**
     * Unit::getCommentTags
     *
     * Extract comment tags from file
     *
     * @param string $filename Name of file to analyze
     * @param type   $type     Type of comments to return
     *
     * @return array
     */
    public function getCommentTags($filename, $type='')
    {
        $this->_scanCommentsForTags($filename, $type);
        return $this->commentTags;
    }
    
    /**
     * Unit::getUnitTags()
     *
     * Build dependency list for testing
     *
     * @param string $filename Name of file to analyze
     *
     * @return array Unit Tags
     */
    public function getUnitTags($filename)
    {
        $this->_scanCommentsForTags($filename);
        $unitTags = array(
                '@expectedException',
                '@expectedExceptionCode',
                '@expectedExceptionMessage',
                '@dataProvider',
                '@depends',
        );
        $return = array();
        foreach ($this->commentTags AS $method=>$values) {
            foreach ($values AS $value) {
                foreach ($value AS $tag=>$comment) {
                    if (in_array($tag, $unitTags)) {
                        $return = array($method=>array(array("$tag"=>"$comment")));
                    }
                }
            }
        }
        return $return;
    }
    
    /**
     * Unit::_scanCommentsForTags()
     *
     * Build dependency list for testing
     *
     * @param string $filename Name of file to be analyzed
     * @param string $type     Type of tags to be returned
     *
     * @return void
     */
    private function _scanCommentsForTags($filename='dbg.php', $type='')
    {
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
        foreach ( $tokens as $token ) {
                
            if (in_array($token[0], $comment)) {
                preg_match_all('/@[a-zA-Z0-9 \t_]*/', $token[1], $matches);
    
                foreach ($matches[0] AS $match) {
                    $firstPos = 0;
                    $rightChar = '';
                    foreach ($whitespace AS $char=>$pos) {
                        if ($pos = strpos($match, $char)) {
                            //dbg::msg("$char $match ");
                            if ($firstPos==0||$pos<$firstPos) {
                                $firstPos = $pos;
                                $rightChar = $char;
                            }
                            $whitespace[$char]=$pos;
                        }
                    }
                    $pivot = $firstPos;
                    
                    dbg::test($pivot!=0, "Pivot is zero");
                    $tag = substr($match, 0, $pivot);
                    $value = substr($match, $pivot+1);
                    array_push($commentTags, array($tag=>$value));
                    $commentsFound = true;
                }
            } else if (($token[0]==T_FUNCTION ||$token[0]==T_CLASS) && $commentsFound) {
                $functionFound = true;
                $commentsFound = false;
            } else if ($token[0]==307 && $functionFound) {
                while ($tag = array_pop($commentTags)) {
                    $this->commentTags[$token[1]] = array($tag);
                }
                $functionFound = false;
            }
        }
    }
}
