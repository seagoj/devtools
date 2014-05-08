<?php

class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $_REQUEST['test']=true;
        $_REQUEST['q']='SPARKS';
        $data = array('firstname'=>'pat', 'lastname'=>'practice');
        $response = new \Devtools\Response(array("firstname"=>"pat", "lastname"=>"practice"));
        $this->assertEquals('OK', $response->status);
        $this->assertEquals($_REQUEST, $response->request);
        $this->assertEquals($data, $response->data);
        unset($_REQUEST);
    }
}
