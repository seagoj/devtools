<?php namespace Devtools;

use Redis;

class RedisRepository
{
    public function __construct(Redis $connection)
    {
        $this->connection = $connection;
    }

    public static function connect()
    {
        return $this->connection->connect('127.0.0.1', 6379);
    }

    public function get($key, $collection = null)
    {
        return is_null($collection) ?
            $this->connection->get($key)  :
            $this->connection->hget($collection, $key);
    }

    public function getAll($collection)
    {
        return $this->connection->hgetall($collection);
    }

    public function set($key, $value, $collection=null)
    {
        return is_null($collection) ?
            $this->connection->set($key, $value) :
            $this->connection->hset($collection, $key, $value);
    }

    public function query($key, $collection=null)
    {
        return $this->get($key, $collection);
    }

    public static function sanitize($queryString)
    {
        return  $queryString;
    }

    public function expire($key, $expiry)
    {
       return $this->connection->expire($key, $expiry);
    }
}
