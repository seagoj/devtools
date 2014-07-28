<?php

class APITest extends PHPUnit_Framework_TestCase
{
    private $testArray;
    private $testResponseStr;
    private $testResponseObj;

    public function setup()
    {
        $this->testArrayJson = array(
            'col1' => 'val1',
            'col2' => 'val2'
        );
        $this->testArrayStr = array(
            $this->testArrayJson
        );
        $this->testResponseStr = "val1|val2\n";
        $this->testResponseObj = '{"status":"OK","request":[],"message":"","col1":"val1","col2":"val2"}';
    }

    public function testAPI()
    {
        $this->assertInstanceOf("Devtools\API", new \Devtools\API());
    }

    public function testFormatResponseStringDefault()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArrayStr
        );
        $this->assertEquals($this->testResponseStr, $response);
    }

    public function testFormatResponseJSON()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArrayJson,
            array('type' => 'json')
        );
        $this->assertEquals($this->testResponseObj, $response);
    }

    /**
     * @expectedException InvalidArgumentException
     **/
    public function testFormatResponseException()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArrayStr,
            array('type' => 'pizza')
        );
    }
}
