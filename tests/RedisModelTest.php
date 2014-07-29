<?php
/**
 * Class RedisModelTest
 * @author Jeremy Seago <seagoj@gmail.com>
 */
class RedisModelTest extends PHPUnit_Framework_TestCase
{
    public function setup()
    {
    }

    public function tearDown()
    {
    }

    public function testSet()
    {
        $key = 'key';
        $value = 'value';
        $dataStore = array();
        $validStore = array($key => $value);
        $stubPredis = $this->getMock('\Predis\Client', array('set'));
        $stubPredis->expects($this->any())
            ->method('set')
            ->will(
                $this->returnCallback(
                    function ($key, $value) use (&$dataStore) {
                        if (!empty($key)) {
                            $dataStore[$key] = $value;
                            return true;
                        } else {
                            return false;
                        }
                    }
                )
            );
        $redis = new \Devtools\RedisModel($stubPredis);

        $this->assertTrue($redis->set($key, $value));
        $this->assertEquals($validStore, $dataStore);
    }

    /**
     * @covers \Devtools\RedisModel::get()
     * @covers \Devtools\RedisModel::query()
     **/
    public function testGet()
    {
        $key = 'key';
        $value = 'value';
        $collection = 'collection';
        $dataStore = array(
            $key => $value,
            $collection => array($key => $value)
        );
        $stubPredis = $this->getMock('\Predis\Client', array('get', 'hget'));
        $stubPredis->expects($this->any())
            ->method('get')
            ->will(
                $this->returnCallback(
                    function ($key) use (&$dataStore) {
                        return isset($dataStore[$key]) ? $dataStore[$key] : false;
                    }
                )
            );
        $stubPredis->expects($this->any())
            ->method('hget')
            ->will(
                $this->returnCallback(
                    function ($collection, $key) use (&$dataStore) {
                        return isset($dataStore[$collection][$key])
                            ? $dataStore[$collection][$key]
                            : false;
                    }
                )
            );
        $redis = new \Devtools\RedisModel($stubPredis);

        $this->assertEquals($value, $redis->get($key));
        $this->assertFalse($redis->get(null));
        $this->assertFalse($redis->get('notkey'));
        $this->assertEquals($value, $redis->get($key, $collection));
        $this->assertFalse($redis->get(null, $collection));
        $this->assertFalse($redis->get('notkey', $collection));
        $this->assertEquals($value, $redis->query($key));
        $this->assertFalse($redis->query(null));
        $this->assertFalse($redis->query('notkey'));
        $this->assertEquals($value, $redis->query($key, $collection));
        $this->assertFalse($redis->query(null, $collection));
        $this->assertFalse($redis->query('notkey', $collection));
    }

    public function testGetAll()
    {
        $key = 'key';
        $value = 'value';
        $collection = 'collection';
        $dataStore = array(
            $collection => array(
                $key => $value
            ),
            'notkey' => 'notvalue'
        );
        $validCollection = array(
            $key => $value
        );
        $stubPredis = $this->getMock('\Predis\Client', array('hgetall'));
        $stubPredis->expects($this->any())
            ->method('hgetall')
            ->will(
                $this->returnCallback(
                    function ($collection) use (&$dataStore) {
                        if (!is_null($collection)) {
                            return $dataStore[$collection];
                        } else {
                            return false;
                        }
                    }
                )
            );
        $redis = new \Devtools\RedisModel($stubPredis);
        $this->assertEquals($validCollection, $redis->getAll($collection));
    }

    public function testSanitize()
    {
        $queryString = 'queryString';
        $this->assertEquals($queryString, \Devtools\RedisModel::sanitize($queryString));
    }
}
