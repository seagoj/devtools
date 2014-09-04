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
abstract class Model
{
    /**
     * Class configuration
     *
     * Stores type of datastore to make the connection to
     **/
    private $config;
    private $connection;

    public abstract function get($key, $collection);
    public abstract function getAll($collection);
    public abstract function set($key, $value, $collection);
    public abstract function query($queryString);
    public static abstract function sanitize($queryString);
    public abstract function connect($options);

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
     * mysqlFetchAll
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

    /**
     * stringify
     *
     * Properly inserts quotes for insertion into sql query
     *
     * @param array   $array     Array of strings to be converted
     * @param boolean $force     Force quotes on each element
     * @param string  $quotation Quote mark to use
     *
     * @return string
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function stringify($array, $force = false, $quotation="'")
    {
        $ret = "";
        if (!is_array($array)) {
            $array = array($array);
        }
        foreach ($array as $element) {
            if (!empty($ret)) {
                $ret .= ",";
            }
            $ret .= (!$force && is_numeric($element))
                ? $element
                : $quotation.$element.$quotation;
        }
        return $ret;
    }
}
