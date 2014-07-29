<?php
/**
 * RedisModel
 *
 * Model for connection to Redis key/value stores
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT: 1.0
 * @link     http://github.com/seagoj/Devtools/RedisModel.php
 **/

/**
 * Class RedisModel
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://github.com/seagoj/Devtools/RedisModel.php
 **/
class RedisModel implements IModel
{
    /**
     * __construct
     *
     * RedisModel constructor
     *
     * @param Array $options Options array
     *
     * @return RedisModel Model object to provide a connection to a redis store
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __construct($options = array())
    {
        $options = array_merge(
            array(
                'scheme' => 'tcp',
                'host'   => '127.0.0.1',
                'port'   => 6379
            ),
            $options
        );
        $this->connection = new \Predis\Client($options);
    }

    /**
     * get
     *
     * Returns value from collection or global store
     *
     * @param String $key        Name of parameter to return value
     * @param String $collection Collection to search for $key
     *
     * @return Mixed Value of $key
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get(\String $key, \String $collection = null)
    {
        return is_null($collection) ?
            $this->connection->get($key)  :
            $this->connection->hget($collection, $key);
    }

    /**
     * getAll
     *
     * Returns all key/value pairs from a collection
     *
     * @param String $collection Collection to retrieve values
     *
     * @return array Array of key/value pairs
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function getAll(\String $collection)
    {
        return $this->connection->hgetall($collection);
    }

    /**
     * set
     *
     * Set value of $key or $collection/$key
     *
     * @param String $key        Key to set value of
     * @param Mixed  $value      Value of key
     * @param String $collection Collection in which $key exists
     *
     * @return boolean Status of value set
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set(\String $key, $value, \String $collection)
    {
        return is_null($collection) ?
            $this->connection->set($key, $value) :
            $this->connection->hset($key, $value, $collection);
    }

    /**
     * query
     *
     * Query store for $key or $collection/$key
     *
     * @param String $key        Name of parameter to return
     * @param String $collection Name of collection to search for $key
     *
     * @return Mixed Value of $key or $collection/$key
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query(\String $key, \String $collection=null)
    {
        return $this->get($key, $collection);
    }
}
