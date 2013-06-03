<?php
/**
 * Model: Model library for PHP
 *
 * @name      Model
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   1.0
 * @link      https://github.com/seagoj/devtools
 *
 */
 
namespace Devtools;

/**
 * Model class for personal MVC framework
 * Only class with knowledge of the database connections
 *
 * @author jds
 */
class Model
{
    /**
     * Class configuration
     *
     * Stores type of datastore to make the connection to
     **/
    private $config;

    /**
     * Model connection
     *
     * Connection object for model
     **/
    private $connection;

    /**
     * Status of connection
     *
     * True if connected; false if not
     **/
    public $connected;

    /**
     * Model::__construct
     *
     * Sets configuration and establishes connection
     *
     * @param   array   $options    Sets options for Model class
     *
     * @return void
     **/
    public function __construct($options = [])
    {
        $defaults = [
            'connect' => true,
            'type' => 'redis',
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        ];

        $this->config = array_merge($defaults, $options);
        if ($this->config['connect']) {
            $this->connect();
        }

        $this->connected = isset($this->connection);
    }

    /**
     * Model::connect
     * 
     * Connects model to datastore
     *
     * @param   array   $options    Options array for host, port and credentials
     *
     * @throws  Exception if datastore type is not supported
     * 
     * @return  boolean     Result of attempted connection
     **/
    public function connect($options = [])
    {
        $this->config = array_merge($this->config, $options);

        $clientOptions = [
            'scheme' => $this->config['scheme'],
            'host' => $this->config['host'],
            'port' => $this->config['port']
        ];

        switch($this->config['type']) {
            case 'redis':
                $this->connection = new \Predis\Client($clientOptions);
                return $this->connected = isset($this->connection);
                break;
            default:
                throw new \Exception($this->config['type']." is not a supported database type");
                break;
        }
    }

    /**
     * Model::set
     *
     * Inserts data into the datastore
     *
     * @param   string  $key    Name given to data value
     * @param   string  $value  Value of data to be stored
     * @param   string  $hash   Hash to be used in key/value store
     *
     * @throws  Exception if datastore type is not supported
     *
     * @return  boolean     Result of insertion
     **/
    public function set($key, $value, $hash = null)
    {
        switch($this->config['type']) {
            case 'redis':
                return is_null($hash) ?
                     $this->connection->set($key, $value) :
                     is_bool($this->connection->hset($hash, $key, $value));
                break;
            default:
                throw new \Exception($this->config['type']." is not a supported database type");
                break;
        }
    }

    /**
     * Model::get
     *
     * Returns data stored under $key
     *
     * @param   string  $key    Name of data to be retrieved
     * @param   string  $hash   Hash to retrieve $key from; defaults to null
     *
     * @throws  Exception if datastore type is not supported
     * 
     * @return  multiple    Data retrieved or false if retrieval fails
     **/
    public function get($key, $hash = null)
    {
        switch($this->config['type']) {
            case 'redis':
                return is_null($hash) ?
                    $this->connection->get($key) :
                    $this->connection->hget($hash, $key);
                break;
            default:
                throw new \Exception($this->config['type'])." is not a supported database type.";
                break;
        }
    }
}
