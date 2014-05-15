<?php

class APITest extends PHPUnit_Framework_TestCase
{
    private $testArray;
    private $testResponseStr;
    private $testResponseObj;

    public function setup()
    {
        $this->testArray = array(
            array(
                'col1' => 'val1',
                'col2' => 'val2'
            )
        );
        $this->testResponseStr = "val1|val2\n";
        $this->testResponseObj = '{"status":"OK","request":[],"message":"","data":[{"col1":"val1","col2":"val2"}]}';
    }

    public function testAPI()
    {
        $this->assertInstanceOf("Devtools\API", new \Devtools\API());
    }

    public function testFormatResponseStringDefault()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArray
        );

        $this->assertEquals($this->testResponseStr, $response);
    }

    /**
    * @runInSeparateProcess
    **/
    public function testFormatResponseJSON()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArray,
            array( 'type' => 'json' )
        );

        $this->assertEquals($this->testResponseObj, $response);
    }

    /**
     * @expectedException InvalidArgumentException
     **/
    public function testFormatResponseException()
    {
        $response = \Devtools\API::formatResponse(
            $this->testArray,
            array( 'type' => 'pizza' )
        );
    }
}
