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
class Model {
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
