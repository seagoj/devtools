<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testPHPResponse()
    {
        $resp = new \Devtools\Response;
        $resp->message("message");
        $resp->data(array("key"=>"value"));
        $this->assertInstanceOf('Devtools\Response', $resp);
        $this->assertTrue(is_object($resp));
        $this->assertEquals('OK', $resp->status);
        $this->assertEquals("message\n", $resp->message);
        $this->assertEquals('value', $resp->key);
    }

    public function testJsonResponse()
    {
        $resp = new \Devtools\Response;
        $resp->message("message");
        $resp->message(array("key"=>"value"));
        $resp->data(array("key"=>"value"));
        $response = $resp->json();
        $this->assertEquals($response, json_encode($resp));
    }
}
