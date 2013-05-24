<?php

namespace config;

class config
{
    public $debug;
    public $conn;
    public $config;

    public function __construct($obj)
    {
        //print get_class($obj);
        $class = explode('\\',get_class($obj));
        $class = $class[sizeof($class)-1];
        //print $class;
        if (strpos($class, 'Test')===strlen($class)-4) {
            $class = substr($class,0,strlen($class)-4);
        }
        //print $class;
        $configFunc='config'.ucfirst($class);
        //print $configFunc;
        if($class!='')
            $this->config = $this->$configFunc();
    }
    private function configDbg()
    {
        $this->debug = true;

        return true;
    }
    private function configDbgTest()
    {
        $this->configDbg();
    }
    private function configModel()
    {
        $this->debug = false;
        $info = array(
                'db'=>'test-model',
                'user'=>'test-model',
                'pass'=>'YcfS9Q7TpSFxhECv',
                'host'=>'localhost',
                'port'=>''
        );
        $this->conn = mysql_connect($info['host'].':'.$info['port'],$info['user'],$info['pass']);
        mysql_select_db($info['db'],$this->conn);

        return true;
    }
    private function configAutoload()
    {
        $this->debug = true;

        return true;
    }
    private function configUnit()
    {
        $this->debug = true;

        return true;
    }
    private function configConfig()
    {
        $this->debug = true;

        return true;
    }
    private function configConfigTest()
    {
        $this->configConfig();
    }
    private function configRandData()
    {
        $this->debug = true;

        return true;
    }
    private function configRandDataTest()
    {
        $this->configRandData();
    }
}
