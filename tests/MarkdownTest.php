<?php

class MarkdownTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setUp()
    {
        $options = array('type'=>'stdout');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
//        unlink(__CLASS__.'.log');
    }

    public function testMarkdown()
    {
        $md = new \Devtools\Markdown();
        $this->assertInstanceOf('Devtools\Markdown', $md);
        $this->_log->write('$md is an instance of Devtools\Markdown','EMPTY');
    }

    public function testHeaders()
    {
        $md = new \Devtools\Markdown();
        $head="";

        for($i=1; $i<=5; $i++) {
            for($count=1; $count<=$i; $count++)
                $head.="#";
            $this->assertEquals("<h$i>H$i</h$i>\n", $md->convert("$head H$i"));
            $head = "";
        }
    }
    
    public function testUnorderedList()
    {
        $md = new \Devtools\Markdown();

        $li = "List Item ";
        $resultStr = "<ul>\n";
        $mdStr = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<li>$li$i</li>\n";
            $mdStr .= "* $li$i\n";
        }

        $resultStr .= "</ul>\n";

        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_tagReplace()
    {
        $sample = __METHOD__." ";
        $resultStr = "";
        $mdStr = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<b>$sample$i</b>";
            $mdStr .= "**$sample$i**";
        }

        $method = new ReflectionMethod('Devtools\Markdown', '_tagReplace');
        $method->setAccessible(true);
        $result = $method->invoke(new \Devtools\Markdown(), $mdStr, 'b', '**');
        $this->assertEquals($resultStr, $result);
    }

    public function testBold()
    {
        $md = new \Devtools\Markdown();

        $sample = __METHOD__." ";
        $resultStr = "";
        $mdStr = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<b>$sample$i</b>";
            $mdStr .= "**$sample$i**";
        }
        
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }
}
