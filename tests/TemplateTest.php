<?php

class TemplateTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function teardown()
    {
    }

    /**
     * @covers Devtools\Template::__construct
     **/
    public function testTemplate()
    {
        $this->assertInstanceOf("Devtools\Template", new \Devtools\Template());
    }

    /**
     * @covers Devtools\Template::autofill
     **/
    public function testAutofill()
    {
        $template = "prefix {{data}} postfix";

        $this->assertEquals(
            \Devtools\Template::autofill($template, ['data'=>7]),
            "prefix 7 postfix"
        );
    }
    
}
