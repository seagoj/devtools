<?php

class MarkdownTest extends PHPUnit_Framework_TestCase
{
    private $_log;

    public function setUp()
    {
        $options = array('file'=>__CLASS__.'.log');
        $this->_log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
        unlink(__CLASS__.'.log');
    }
    public function test_formatInline()
    {
        $md = new \Devtools\Markdown();

        $sample = "*Test*\n";
        $output = "<ul>\n<li>Test</li>\n</ul>\n\n";
        $swap = false;
        
        $syntax = '*';

        foreach(explode("\n", $sample) as $line)  {
            $first = strpos($line, $syntax);
            if($first!==false) {
                $second = strpos($line, $syntax, $first+strlen($syntax));
            }
            if($first!==false && $second!==false) {
                $swap = true;    
            }
        }

        $this->assertEquals($swap, true);
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
        $mdStrStar = $mdStrMinus = $mdStrPlus = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<li>$li$i</li>\n";
            $mdStrStar .= "* $li$i\n";
            $mdStrMinus .= "- $li$i\n";
            $mdStrPlus .= "+ $li$i\n";
        }

        $resultStr .= "</ul>\n";
        $mdStrStar .= "\n";
        $mdStrMinus .= "\n";
        $mdStrPlus .= "\n";

        $this->assertEquals($resultStr, $md->convert($mdStrStar));
        $this->assertEquals($resultStr, $md->convert($mdStrMinus));
        $this->assertEquals($resultStr, $md->convert($mdStrPlus));
    }

    public function testOrderedList()
    {
        $md = new \Devtools\Markdown();

        $li = "List Item ";
        $resultStr = "<ol>\n";
        $mdStr = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<li>$li$i</li>\n";
            $mdStr .= "$i. $li$i\n";
        }

        $resultStr .= "</ol>\n";
        $mdStr .= "\n";

        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_tagReplace()
    {
        $sample = __METHOD__." ";
        $resultStr = "";
        $mdStr = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<b>$sample$i</b> ";
            $mdStr .= "**$sample$i** ";
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
        $resultStr = $mdStrStar = $mdStrUS = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<b>$sample$i</b> ";
            $mdStrStar .= "**$sample$i** ";
            $mdStrUS .= "__".$sample.$i."__ ";
        }
        
        $resultStr = "<p>\n$resultStr\n</p>\n";

        $this->assertEquals($resultStr, $md->convert($mdStrStar));
        $this->assertEquals($resultStr, $md->convert($mdStrUS));
    }

    public function testItalics()
    {
        $md = new \Devtools\Markdown();

        $sample = __METHOD__." ";
        $resultStr = $mdStrStar = $mdStrUS = "";

        for($i=1; $i<=5; $i++) {
            $resultStr .= "<i>$sample$i</i> ";
            $mdStrStar .= "*$sample$i* ";
            $mdStrUS .= "_".$sample.$i."_ ";
        }
        
        $resultStr = "<p>\n$resultStr\n</p>\n";

        $this->assertEquals($resultStr, $md->convert($mdStrStar));
        $this->assertEquals($resultStr, $md->convert($mdStrUS));
    }

    public function testInlineCode()
    {
        $md = new \Devtools\Markdown();

        $mdStr = "not code `code` not code\n";
        $resultStr = "<p>\nnot code <code>code</code> not code\n</p>\n";

        $this->assertEquals($resultStr, $md->Convert($mdStr));
    }

    public function testHR()
    {
        $md = new \Devtools\Markdown();

        $mdStr = "---\n";
        $resultStr = "<hr>\n";

        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_formatCode()
    {
        $mdStr = "    code1\n    code2\n";
        $resultStr = "<code>\n\tcode1\n\tcode2\n</code>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_formatBlockquote()
    {
        $mdStr = "> line1\n> line2\n";
        $resultStr = "<blockquote>\n\t<p>line1</p>\n\t<p>line2</p>\n</blockquote>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_formatImage()
    {
        $mdStr = "![Alt Text](http://pathtoimage.com/image.jpg)\n";
        $resultStr = "<img src='http://pathtoimage.com/image.jpg' alt='Alt Text' />\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function test_formatLink()
    {
        $mdStr = "[link](http://google.com)\n";
        $resultStr = "<a href='http://google.com' >link</a>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    public function testFullPage()
    {
        $mdStr = file_get_contents('tests/test.markdown');
        $resultStr = file_get_contents('tests/test.html');

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }
}

