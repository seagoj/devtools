<?php

/**
 * @covers Devtools\Markdown
 **/
class MarkdownTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        $options = array('type' => 'stdout');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     **/
    public function testValidateConfigValid()
    {
        $valid = [
            'flavor' => 'standard',
            'logType' => 'stdout'
        ];

        $this->assertInstanceOf(
            'Devtools\Markdown', 
            new \Devtools\Markdown($valid)
        );
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     *
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    color is not a valid option.
     **/
    public function testValidateConfigInvalidVar()
    {
        $invalidVar = [
            'color' => 'purple'
        ];
   
        new \Devtools\Markdown($invalidVar);
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     *
     * @expectedException           InvalidArgumentException
     * @expectedExceptionMessage    savory is not a valid value for flavor
     **/
    public function testValidateConfigInvalidValue()
    {
        $invalidValue = [
            'flavor' => 'savory'
        ];

        new \Devtools\Markdown($invalidValue);
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     **/
    public function testMarkdown()
    {
        $md = new \Devtools\Markdown();
        $this->assertInstanceOf('Devtools\Markdown', $md);
    }

    /*
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatHeader
     **/
    public function testHeaders()
    {
        $md = new \Devtools\Markdown();
        $head="";

        for ($i=1; $i<=5; $i++) {
            for($count=1; $count<=$i; $count++)
                $head.="#";
            $this->assertEquals("<h$i>H$i</h$i>\n", $md->convert("$head H$i"));
            $head = "";
        }
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatUnorderedList
     **/
    public function testUnorderedList()
    {
        $md = new \Devtools\Markdown();

        $li = "List Item ";
        $resultStr = "<ul>\n";
        $mdStrStar = $mdStrMinus = $mdStrPlus = "";

        for ($i=1; $i<=5; $i++) {
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

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatOrderedList
     **/
    public function testOrderedList()
    {
        $md = new \Devtools\Markdown();

        $li = "List Item ";
        $resultStr = "<ol>\n";
        $mdStr = "";

        for ($i=1; $i<=5; $i++) {
            $resultStr .= "<li>$li$i</li>\n";
            $mdStr .= "$i. $li$i\n";
        }

        $resultStr .= "</ol>\n";
        $mdStr .= "\n";

        $this->assertEquals($resultStr, $md->convert($mdStr));

        $this->assertEquals("<p>\ntest. test\n</p>\n", $md->convert("test. test"));
    }

    /**
     * @covers Devtools\Markdown::tagReplace
     **/
    public function testTagReplace()
    {
        $sample = __METHOD__." ";
        $resultStr = "";
        $mdStr = "";

        for ($i=1; $i<=5; $i++) {
            $resultStr .= "<b>$sample$i</b> ";
            $mdStr .= "**$sample$i** ";
        }

        $method = new ReflectionMethod('Devtools\Markdown', 'tagReplace');
        $method->setAccessible(true);
        $result = $method->invoke(new \Devtools\Markdown(), $mdStr, 'b', '**');
        $this->assertEquals($resultStr, $result);
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatInline
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testBold()
    {
        $md = new \Devtools\Markdown();

        $sample = __METHOD__." ";
        $resultStr = $mdStrStar = $mdStrUS = "";

        for ($i=1; $i<=4; $i++) {
            $resultStr .= "<strong>$sample$i</strong> ";
            $mdStrStar .= "**$sample$i** ";
            $mdStrUS .= "__".$sample.$i."__ ";
        }

        $resultStr .= "<strong>".$sample."5</strong>";
        $mdStrStar .= "**".$sample."5**";
        $mdStrUS .= "__".$sample."5__";

        $resultStr = "<p>\n$resultStr\n</p>\n";

        $this->assertEquals($resultStr, $md->convert($mdStrStar));
        $this->assertEquals($resultStr, $md->convert($mdStrUS));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatInline
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testItalics()
    {
        $md = new \Devtools\Markdown();

        $sample = __METHOD__." ";
        $resultStr = $mdStrStar = $mdStrUS = "";

        for ($i=1; $i<=5; $i++) {
            $resultStr .= "<em>$sample$i</em> ";
            $mdStrStar .= "*$sample$i* ";
            $mdStrUS .= "_".$sample.$i."_ ";
        }

        $resultStr = "<p>\n$resultStr\n</p>\n";

        $this->assertEquals($resultStr, $md->convert($mdStrStar));
        $this->assertEquals($resultStr, $md->convert($mdStrUS));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatInline
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testInlineCode()
    {
        $md = new \Devtools\Markdown();

        $mdStr = "not code `code` not code\n";
        $resultStr = "<p>\nnot code <code>code</code> not code\n</p>\n";

        $this->assertEquals($resultStr, $md->Convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatHR
     **/
    public function testHR()
    {
        $md = new \Devtools\Markdown();

        $mdStr = "---\n";
        $resultStr = "<hr>\n";

        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatCode
     **/
    public function testFormatCode()
    {
        $mdStr = "    code1\n    code2\n";
        $resultStr = "<code>\n\tcode1\n\tcode2\n</code>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatBlockquote
     **/
    public function testFormatBlockquote()
    {
        $mdStr = "> line1\n> line2\n";
        $resultStr = "<blockquote>\n    line1\n    line2\n</blockquote>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::getTextPath
     * @covers Devtools\Markdown::formatImage
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testFormatImage()
    {
        $mdStr = "![Alt Text](http://pathtoimage.com/image.jpg)\n";
        $resultStr = "<p>\n<img src='http://pathtoimage.com/image.jpg' alt='Alt Text' />\n</p>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::getTextPath
     * @covers Devtools\Markdown::formatLink
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testFormatLink()
    {
        $mdStr = "[link](http://google.com)\n";
        $resultStr = "<p>\n<a href='http://google.com' >link</a>\n</p>\n";

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }

    /**
     * @covers Devtools\Markdown::__construct
     * @covers Devtools\Markdown::validateConfig
     * @covers Devtools\Markdown::convert
     * @covers Devtools\Markdown::formatParagraph
     **/
    public function testFullPage()
    {
        $mdStr = 'tests/test.markdown';
        $resultStr = file_get_contents('tests/test.html');

        $md = new \Devtools\Markdown();
        $this->assertEquals($resultStr, $md->convert($mdStr));
    }
}
