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
        file_put_contents($file = 'template.html', $template);
        $data = ['data' => 7];
        $result = "prefix 7 postfix";

        $this->assertEquals(
            \Devtools\Template::autofill($template, $data),
            $result
        );

        $this->assertEquals(
            \Devtools\Template::autofill($file, $data),
            $result
        );

        unlink($file);
    }
    
}
