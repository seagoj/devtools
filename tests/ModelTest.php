<?php

class ModelTest extends PHPUnit_Framework_TestCase
{
    private $log;

    public function setUp()
    {
        $options = array('type'=>'stdout');
        $this->log = new \Devtools\Log($options);
    }

    public function tearDown()
    {
    }

    public function testConnection()
    {
        $options = ['connect' => false];
        $this->assertInstanceOf("Devtools\Model", ($model = new \Devtools\Model($options)));
        $this->assertFalse($model->connected);
        $model->connect();
        $this->assertTrue($model->connected);
    }

    public function testSetGet()
    {
        $model = new \Devtools\Model();

        $this->assertTrue($model->set('Method', __METHOD__));
        $this->assertEquals($model->get('Method'), __METHOD__);
    }
}
