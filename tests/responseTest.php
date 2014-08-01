<?php
class ResponseTest extends PHPUnit_Framework_TestCase
{
    public function testJson()
    {
        $validResponse = json_encode(array('status'=>'OK', 'request'=>array(), 'message'=>''));
        $responseSuppressed = new \Devtools\Response(array('suppress_header'=>true));
        $this->assertEquals($validResponse, $responseSuppressed->json());
        $this->assertTrue(!in_array('json', headers_list()));
        $response = new \Devtools\Response;
        $this->assertEquals($validResponse, $response->json());
        echo ob_get_clean();
    }

    /**
     * @covers \Devtools\Response::__construct()
     * @covers \Devtools\Response::data()
     **/
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

    /**
     * @covers \Devtools\Response::data()
     * @expectedException \Exception
     **/
    public function testData()
    {
        $response = new \Devtools\Response;
        $response->data(array());
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

    /**
     * @covers Devtools\Response::getSuppressHeader()
     **/
    public function testGetSuppressHeader()
    {
        $this->assertEquals(
            array('suppress_header'=>false),
            \Devtools\Response::getsuppressHeader()
        );
        $_REQUEST['suppress_header'] = true;
        $this->assertEquals(
            array('suppress_header'=>true),
            \Devtools\Response::getsuppressHeader()
        );
        $_REQUEST['suppress_header'] = false;
        $this->assertEquals(
            array('suppress_header'=>false),
            \Devtools\Response::getsuppressHeader()
        );
        unset($_REQUEST);
    }

    /**
     * @covers \Devtools\Response::ajax()
     **/
    public function testAjax()
    {
        $request = array('col1', 'val1');
        $call = \Devtools\Response::ajax('tests/ajaxSuccess.php', $request);
        $this->assertEquals($request, $call);
    }

    /**
     * @covers \Devtools\Response::getRequest()
     **/
    public function testGetRequest()
    {
        $expectedValue = array('var1'=>'val1', 'var2'=>'val2');
        $_REQUEST = array('var1'=>'val1', 'var3'=>'val3');
        $this->assertEquals($expectedValue, \Devtools\Response::getRequest(array(
            'var1',
            'var2'=>'val2'
        )));
    }
}
