<?php
/**
 * Class MysqlModelTest
 * @author Jeremy Seago <seagoj@gmail.com>
 */
class MysqlModelTest extends PHPUnit_Framework_TestCase
{
   /**
     * @expectedException \Exception
     **/
    public function testConnectFail()
    {
        \Devtools\MysqlModel::connect(array());
    }

    public function testConnect()
    {
        $options = array(
            'type' => 'mysql',
            'host'=> '192.168.0.7',
            'db'=> 'bps',
            'username' => 'root',
            'password' => 'BPS4mysql'
        );
        $this->assertInstanceOf(
            'PDO',
            \Devtools\MysqlModel::connect($options)
        );
    }

    public function testGet()
    {
        $collection = 'collection';
        $collectionKey = 'collectionKey';
        $collectionValue = 'collectionValue';
        $key = 'key';
        $value = 'value';
        $dataStore = array($collection=>array($collectionKey, $collectionValue), $key=>$value);
        $stubPDO = $this->getMockBuilder('PDOMock')
            ->getMock();
        $this->assertEquals(array(array('key'=>'value')), $stubPDO->fetch(2));
        $mysql = new \Devtools\MysqlModel($stubPDO);
        /* $this->assertEquals( */
        /*     array($collectionKey=>$collectionValue), */
        /*     $mysql->get($key, $collection) */
        /* ); */
    }
}

class PDOMock extends \PDO
{
    private $queryString;

    public function __construct() {}
    public function prepare($string)
    {
        return $string === 'SELECT :key FROM :collection';
    }
    public function execute($params)
    {
        return $params === array(
            'key'=>'key',
            'collection'=>'collection'
        );
    }
    public function fetch($type)
    {
        var_dump('called');
        var_dump($type);
        /* switch($type) { */
        /*     case 2: */
                return array(array('key'=>'value'));
            /* default: */
            /*     throw new \Exception('Invalid fetch type.'); */
        /* } */
    }
}
