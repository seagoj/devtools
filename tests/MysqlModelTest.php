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
        $stubPDO->expects($this->any())
        ->method('get')
        ->will(
            $this->returnCallback(
                function ($key, $collection) use (&$dataStore) {
                    if (!empty($collection)) {
                        return $dataStore[$collection][$key];
                    } else {
                       return $dataStore[$key];
                    }
                }
            )
        );
        $mysql = new \Devtools\MysqlModel($stubPDO);
        $this->assertEquals(
            array($collectionKey=>$collectionValue),
            $this->$mysql->get($key, $collection)
        );
    }
}

class PDOMock extends \PDO
{
    public function __construct() {}
}
