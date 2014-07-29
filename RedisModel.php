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
     * @param String $key        Name of parameter to retrieve
     * @param String $collection Collection to search for $key
     *
     * @return Mixed Value of $key
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get(\String $key, \String $collection = null)
    {

    }
}
