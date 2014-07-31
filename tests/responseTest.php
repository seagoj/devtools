<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testResponse()
    {
        $_REQUEST['test']=true;
        $_REQUEST['q']='SPARKS';
        $data = array('firstname'=>'pat', 'lastname'=>'practice');
        $validData = array(
            'status' => 'OK',
            'request' => array(
                'test' => true,
                'q' => 'SPARKS'
            ),
            'message'=> '',
            'firstname' => 'pat',
            'lastname' => 'practice'
        );
        $response = new \Devtools\Response(array("firstname"=>"pat", "lastname"=>"practice"));
        $this->assertEquals('OK', $response->status);
        $this->assertEquals($_REQUEST, $response->request);
        $response->data($data);
        $this->assertEquals(json_encode($validData), $response->json());
        unset($_REQUEST);
    }

    public function testJson()
    {
        $validResponse = json_encode(array('status'=>'OK', 'request'=>array(), 'message'=>''));
        $response = new \Devtools\Response;
        $this->assertEquals($validResponse, $response->json());
    }

    /**
     * @covers Devtools\Response::message()
     **/
    public function testMessage()
    {
        $validResponse = json_encode(array('status'=>'OK', 'request'=>array(), 'message'=>'Message.'."\n"));
        $response = new \Devtools\Response;
        $response->message("Message.");
        $this->assertEquals($validResponse, $response->json());
    }

    /**
    * @covers Devtools\Response::message()
    **/
    public function testMessageArray()
    {
        $validResponse = json_encode(
            array(
                'status'=>'OK',
                'request'=>array(),
                'message'=>var_export(array('key'=>'value'), true)."\n"
            )
        );
        $response = new \Devtools\Response;
        $response->message(array('key'=>'value'));
        $this->assertEquals($validResponse, $response->json());
    }

    /**
     * @covers Devtools\Response::message()
     * @expectedException \Exception
     **/
    public function testMessageFail()
    {
        $validResponse = json_encode(
        array(
            'status'    => '',
            'request'   => array(),
            'message'   => 'Error.'."\n"
        )
        );
        $responseFail = new \Devtools\Response;
        $responseFail->message("Error.", true);
        $this->assertEquals($validResponse, $responseFail->json());
    }

    /**
     * @covers Devtools\Response::fail()
     * @expectedException \Exception
     **/
    public function testFail()
    {
        $validResponse = json_encode(
            array(
                'status'    => '',
                'request'   => array(),
                'message'   => 'Error.'."\n"
            )
        );
        $response = new \Devtools\Response;
        $response->fail("Error.");
        $this->assertEquals($validResponse, $response->json());
    }
}
