<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testPHPResponse()
    {
        $resp = new \Devtools\Response;
        $resp->message = "message";
        $resp->data = array("key"=>"value");
        $_REQUEST['language'] = 'php';
        var_dump($resp->send());
    }
}
