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

require_once 'autoloader.php';

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
    public function __construct($options = array())
    {
        // require_once 'autoloader.php';

        if(is_object($options)) {
            $options = (array) $options;
        }

        $defaults = array(
            'connect' => true,
            'type' => 'redis',
            'scheme' => 'tcp',
            'host' => '127.0.0.1',
            'port' => 6379
        );

/*        if(empty($options) && is_file('model.json')) {
            $options = json_decode(file_get_contents('model.json'));
        }
*/
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
            'redis',
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
     * @param   array   $options    Options array for host, port and credentials
     *
     * @throws  Exception if datastore type is not supported
     *
     * @return  boolean     Result of attempted connection
     **/
    public function connect($options = array())
    {
        $this->config = array_merge($this->config, $options);

        $clientOptions = array(
            'scheme' => $this->config['scheme'],
            'host' => $this->config['host'],
            'port' => $this->config['port']
        );

        $this->validateConfig();

        $func = 'connect'.ucfirst($this->config['type']);
        return $this->$func();
    }

    /**
     * Model::connectRedis
     *
     * Connects model to redis datastore
     *
     * @return  boolean     Status of connection
     **/
    private function connectRedis()
    {
        $clientOptions = array(
            'scheme' => $this->config['scheme'],
            'host' => $this->config['host'],
            'port' => $this->config['port']
        );

        $this->connection = new \Predis\Client($clientOptions);
        return $this->connected = isset($this->connection);
    }

    private function connectFirebird()
    {
        $this->connection = \ibase_pconnect(
            $this->config['host'].':C:\\'.$this->config['environment'].'\\'.$this->config['location'].'\\CMPDWIN.PKF',
            $this->config['dba'],
            $this->config['password']
        );
        if(!$this->connection) throw new \Exception('connection to host could not be established');
        return $this->connected = isset($this->connection);
    }

    /**
     * Model::setRedis
     *
     * Performs the set operation on redis datastores
     *
     * @param   string  $key    Name given to data value
     * @param   string  $value  Value of data to be stored
     * @param   string  $hash   Hash to be used in key/value store
     *
     * @return  boolean Result of insertion
     **/
    private function setRedis($key, $value, $hash)
    {
        return is_null($hash) ?
            $this->connection->set($key, $value) :
            is_bool($this->connection->hset($hash, $key, $value));
    }

    /**
     * Model::getRedis
     *
     * Performs the get operation on redis datastores
     *
     * @param   string  $key    Name of data to be retrieved
     * @param   string  $hash   Hash modifier for name
     *
     * @return  multi   Value of data retrieved
     **/
    private function getRedis($key, $hash)
    {
        return is_null($hash) ?
            $this->connection->get($key) :
            $this->connection->hget($hash, $key);
    }

    /**
     * Model::expireRedis
     *
     * Performs the expiration operation on redis datastores
     *
     * @param   string  $key    Name of data to be modified
     * @param   integer $expiry Expiration to be applied
     *
     * @return  boolean Status of setting the expiration
     **/
    private function expireRedis($key, $expiry)
    {
        return $this->connection->expire($key, $expiry);
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
            $this->checkConnection();
        $func = 'set'.ucfirst($this->config['type']);
        return $this->$func($key, $value, $hash);
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
        $this->checkConnection();

        $func = 'get'.ucfirst($this->config['type']);
        return $this->$func($key, $hash);
    }

    /**
     * Model::getAll
     *
     * Returns all keys related to $hash
     *
     * @param string $hash Hash used to lookup keys
     *
     * @return array Array of keys and values
     **/
    public function getAll($hash)
    {
        $this->checkConnection();

        $func = 'getAll'.ucfirst($this->config['type']);
        return $this->$func($hash);
    }

    /**
     * Model::getAllRedis
     *
     * Returns all keys related to $hash from a redis datastore
     *
     * @param string $hash Hash used to lookup keys
     *
     * @return array Array of keys and values
     **/
    private function getAllRedis($hash)
    {
        return $this->connection->hgetall($hash);
    }

    /**
     * Model::expire
     *
     * Sets expiration period for a particular data entry
     *
     * @param   string  $key    Key to set the expiration for
     * @param   string  $expiry Expiration
     *
     * @throws  Exception if datastore type is not supported
     *
     * @return  boolean Result of setting the expiration
     **/
    public function expire($key, $expiry)
    {
        $this->checkConnection();

        $func = 'expire'.ucfirst($this->config['type']);
        return $this->$func($key, $expiry);
    }

    /**
     * Model::sanitize
     *
     * Sanitizes data for manipulation in PHP
     *
     * @param   string  $data   Data to be sanitized
     * @param   string  $type   Type of system to sanitize for
     *
     * @throws  Exception if data cannot be sanitized
     *
     * @return  string  Sanitized data
     **/
    public function sanitize($data, $type = 'html')
    {
        switch($type) {
            case 'html':
                $data = htmlspecialchars($data);
                break;
            /*
            case 'mysql':
                $data = mysql_real_escape_string($data);
                break;
            */
            case 'shellcmd':
                $data = escapeshellcmd($data);
                break;
            case 'shellarg':
                $data = escapeshellarg($data);
                break;
        }

        return $data;
    }

    public function query($sql, $reduce=true, $debug=false)
    {
        if(!is_array($this->config)) {
            throw new \Exception("Options array is not an array.");
        }

        $func = 'query'.ucfirst($this->config['type']);
        return $this->$func($sql, $reduce, $debug);
    }

    private function queryFirebird($sql, $reduce, $debug)
    {
        if($debug) \Devtools\Log::consoleLog($this->connection);
        if($debug) \Devtools\Log::consoleLog($sql);

        $sql = str_replace("\'", "''", $sql);

        $this->debugLog->write($sql);
        $q = ibase_query($this->connection, $sql);
        $this->debugLog->write($q);
        if($debug) \Devtools\Log::consoleLog($q);
        if (!(is_bool($q) || is_int($q))) {
            $this->debugLog->write('valid');
            $result = array();
            while($row = ibase_fetch_assoc($q, IBASE_TEXT)) {
                $this->debugLog->write($row);
                array_push($result, $row);
            }

            ibase_free_result($q);
            if($debug) \Devtools\Log::consoleLog($result);
        } else {
            $result = $q;
        }

        $this->debugLog->write($result);

        return ($reduce ? $this->reduceResult($result) : $result);
    }

    private function reduceResult($result)
    {
        if(is_array($result) && (count($result) == 1)) {
            reset($result);
            return $this->reduceResult($result[key($result)]);
        } else {
            return $result;
        }
    }
}
