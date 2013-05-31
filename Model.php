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
            'type' => 'redis'
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

        switch($this->config['type']) {
            case 'redis':
                $this->connection = new \Predis\Client();
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
     *
     * @throws  Exception if datastore type is not supported
     *
     * @return  boolean     Result of insertion
     **/
    public function set($key, $value)
    {
        switch($this->config['type']) {
            case 'redis':
                return $this->connection->set($key, $value);
                break;
            default:
                throw new \Exception($this->config['type']." is not a support database type");
                break;
        }
    }

    /**
     * Model::get
     *
     * Returns data stored under $key
     *
     * @param   string  $key    Name of data to be retrieved
     *
     * @throws  Exception if datastore type is not supported
     * 
     * @return  multiple    Data retrieved or false if retrieval fails
     **/
    public function get($key)
    {
        switch($this->config['type']) {
            case 'redis':
                return $this->connection->get($key);
                break;
            default:
                throw new \Exception($this->config['type'])." is not a supported database type.";
                break;
        }
    }
}
