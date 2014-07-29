<?php
/**
 * Model
 *
 * Model library for PHP
 *
 * PHP version 5.3
 *
 * @name      Model
 * @category  Seagoj
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @version   GIT: 1.0
 * @link      https://github.com/seagoj/Devtools
 */

namespace Devtools;

/**
 * Model class for personal MVC framework
 * Only class with knowledge of the database connections
 *
 * @category  Seago
 * @package   Devtools
 * @author    Jeremy Seago <seagoj@gmail.com>
 * @copyright 2012 Jeremy Seago
 * @license   http://opensource.org/licenses/mit-license.php, MIT
 * @link      https://github.com/seagoj/Devtools
 *
 * @method    string set(string $key, mixed $value);
 * @method    string get(string $key);
 * @method    string hset(string $collection, string $key, mixed $value);
 * @method    string hget(string $collection, string $key);
 * @method    string hgetall(string $collection);
 * @method    string expire(string $key, string $collection);
 */
class Model implements \Devtools\IModel
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
     * Log
     *
     * Debug Log object
     **/
    private $debugLog;

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
     * @param array $options Sets options for Model class
     *
     * @return void
     **/
    public function __construct($options = array())
    {
        if (is_object($options)) {
            $options = (array) $options;
        }
        $defaults = array(
            'connect' => true,
            'type' => 'firebird',
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        );
        $this->config = array_merge($defaults, $options);
        $this->validateConfig();
        if ($this->config['connect']) {
            $this->connect();
        }
        $this->connected = isset($this->connection);
        $this->debugLog = \Devtools\Log::debugLog();
    }

    /**
     * Model::validate
     *
     * Encapsulates configuration validation in one function called prior to
     * connection so it can be ignored in the reset of the class.
     *
     * @throws  Exception if datastore type is not supported
     *
     * @return  boolean    Status of validation
     **/
    private function validateConfig()
    {
        $validTypes = array(
            'firebird'
        );
        if (in_array($this->config['type'], $validTypes)) {
            return true;
        } else {
            throw new \Exception($this->config['type']." is not a supported datastore type.");
        }
    }

    /**
     * Model::checkConnection()
     *
     * Checks for valid connection prior to performing an action on the
     * datastore
     *
     * @throws  Exception if connection does not exist
     *
     * @return  Boolean     Status of connection
     **/
    private function checkConnection()
    {
        if ($this->connected === true) {
            return true;
        } else {
            throw new \Exception("Connection is not established.");
        }
    }

    /**
     * Model::connect
     *
     * Connects model to datastore
     *
     * @param Array $options Options array for host, port and credentials
     *
     * @throws Exception If datastore type is not supported
     * @return Boolean   Result of attempted connection
     **/
    public function connect($options = array())
    {
        $this->config = array_merge($this->config, $options);
        $this->validateConfig();
        $func = 'connect'.ucfirst($this->config['type']);
        return $this->$func();
    }

    /**
     * connectFirebird
     *
     * Connect to a firebird database
     *
     * @return boolean True if connected
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    private function connectFirebird()
    {
        $this->connection = \ibase_pconnect(
            $this->config['host'].':C:\\'
            .$this->config['environment'].'\\'
            .$this->config['location'].'\\CMPDWIN.PKF',
            $this->config['dba'],
            $this->config['password']
        );
        if (!$this->connection) {
            throw new \Exception('connection to host could not be established');
        }
        return $this->connected = isset($this->connection);
    }

    /**
     * Model::set
     *
     * Inserts data into the datastore
     *
     * @param string $key        Name given to data value
     * @param mixed  $value      Value of data to be stored
     * @param string $collection Collection to search for $key
     *
     * @throws  Exception if datastore type is not supported
     * @return  boolean     Result of insertion
     **/
    public function set($key, $value, $collection=null)
    {
        $this->checkConnection();
        return $this->run('set', func_get_args());
    }

    /**
     * run
     *
     * Runs method and params
     *
     * @param String $method Method to run
     * @param String $params Parameters for method
     *
     * @return mixed Return of method
     */
    private function run($method, $params)
    {
        return call_user_func_array(
            array(
                $this,
                $method.ucfirst($this->config['type'])
            ),
            $params
        );
    }

    /**
     * Model::get
     *
     * Returns data stored under $key
     *
     * @param String $key        Name of data to be retrieved
     * @param String $collection Hash to retrieve $key from; defaults to null
     *
     * @throws  Exception if datastore type is not supported
     * @return  multiple    Data retrieved or false if retrieval fails
     **/
    public function get($key, $collection = null)
    {
        $this->checkConnection();
        return $this->run('get', func_get_args());
    }

    /**
     * Model::getAll
     *
     * Returns all keys related to $collection
     *
     * @param String $collection Hash used to lookup keys
     *
     * @return array Array of keys and values
     **/
    public function getAll($collection)
    {
        $this->checkConnection();
        return $this->run('getAll', func_get_args());
    }

    /**
     * Model::expire
     *
     * Sets expiration period for a particular data entry
     *
     * @param String $key    Key to set the expiration for
     * @param String $expiry Expiration
     *
     * @throws  Exception if datastore type is not supported
     * @return  boolean Result of setting the expiration
     **/
    public function expire($key, $expiry)
    {
        $this->checkConnection();
        $func = 'expire'.ucfirst($this->config['type']);
        return $this->$func($key, $expiry);
    }


    /**
     * query
     *
     * Run query against the model
     *
     * @param String  $sql    Query string
     * @param Boolean $reduce Reduce result if true
     *
     * @return mixed Return from method
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query($sql, $reduce=true)
    {
        if (!is_array($this->config)) {
            throw new \Exception("Options array is not an array.");
        }
        $func = 'query'.ucfirst($this->config['type']);
        return $this->$func($sql, $reduce);
    }

    /**
     * queryFirebird
     *
     * Query firebird model
     *
     * @param String  $sql    Query string
     * @param Boolean $reduce Reduce result if true
     *
     * @return mixed Result of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    private function queryFirebird($sql, $reduce)
    {
        $sql = str_replace("\'", "''", $sql);
        if (gettype($this->connection) === 'resource') {
            $q = ibase_query($this->connection, $sql);
            if (!(is_bool($q) || is_int($q))) {
                $result = array();
                while ($row = ibase_fetch_assoc($q, IBASE_TEXT)) {
                    array_push($result, $row);
                }
                ibase_free_result($q);
            } else {
                $result = $q;
            }
            return ($reduce ? $this->reduceResult($result) : $result);
        } else {
            throw new \InvalidArgumentException('Invalid connection type.');
        }
    }

    /**
     * reduceResult
     *
     * Reduce result
     *
     * @param Array $result Array to reduce
     *
     * @return mixed Reduced result
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    protected function reduceResult($result)
    {
        if (is_array($result) && (count($result) == 1)) {
            reset($result);
            return $this->reduceResult($result[key($result)]);
        } else {
            return $result;
        }
    }

    /**
     * mysql_fetch_all
     *
     * Compiles results of query into multidimensional hash
     *
     * @param Resource $resource    Model resource
     * @param Integer  $result_type MYSQL_BOTH, MYSQL_NUM, MYSQL_ASSOC
     *
     * @return array Hash of query results
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function mysqlFetchAll($resource, $result_type=MYSQL_BOTH)
    {
        $result = array();
        while ($row = mysql_fetch_array($resource, $result_type)) {
            array_push($result, $row);
        }
        return $result;
    }
}
