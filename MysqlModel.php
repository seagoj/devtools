<?php
/**
 * MysqlModel
 *
 * Model for MySQL databases
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT:
 * @link     http://github.com/seagoj/Devtools/MysqlModel.php
 **/

namespace Devtools;

/**
 * MysqlModel
 *
 * Model for MySQL databases
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  DEVTOOLS
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @link     http://github.com/seagoj/Devtools/MysqlModel.php
 **/
class MysqlModel implements IModel
{
    /**
     * MysqlModel::__construct
     *
     * Uses passed connection or establishes connection with defaults
     *
     * @param \PDO $connection PDO connection to a DB
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function __construct(\PDO $connection=null)
    {
        if (is_null($connection)) {
            $connection = \Devtools\MysqlModel::connect(
                array(
                    'type'     => 'mysql',
                    'host'     => 'localhost',
                    'db'       => 'database',
                    'username' => 'username',
                    'password' => 'password'
                )
            );
        }
        $this->connection = $connection;
    }

    /**
     * MysqlModel::connect
     *
     * Establish connection if one is not passed
     *
     * @param array $options Options array [
     *     'type':     'mysql',     // Type of DB connection
     *     'host':     'localhost', // DB Host
     *     'db':       'database',  // DB Name
     *     'username': 'username',  // DB User
     *     'password': 'password',  // DB Password
     * ]
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function connect($options)
    {
        $keys = array_keys($options);
        if (in_array('type',  $keys)
            && in_array('host', $keys)
            && in_array('db', $keys)
            && in_array('username', $keys)
            && in_array('password', $keys)
        ) {
            return  new \PDO(
                sprintf(
                    "%s:host=%s;dbname=%s",
                    $options['type'],
                    $options['host'],
                    $options['db']
                ),
                $options['username'],
                $options['password']
            );
        } else {
            throw new \Exception('Invalid connection options.');
        }
    }

    /**
     * MysqlModel::get
     *
     * Retrieve value from Model based on passed variables
     *
     * @param string|array $key        Name of value to return
     * @param string|array $collection Collection (table) to search for key
     * @param string|array $where      Optional: Where clause
     *
     * @return array|string|integer Value of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function get($key, $collection, $where=null)
    {
        $sql = "SELECT ".(MysqlModel::stringify($key, true, '`'))
            ." FROM $collection";

        if (!is_null($where)) {
            // Initializes $where and $params
            extract(
                $this->buildWhere($where)
            );
            $sql .= $where;
        } else {
            $params = null;
        }

        return $this->query($sql, $params);
    }

    /**
     * buildWhere
     *
     * Builds where clause from passed object
     *
     * @param array $where build where clause from array
     *
     * @return string
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    private function buildWhere($where)
    {
        $params = array();
        $first = true;
        foreach ($where as $field => $value) {
            if (!$first) {
                $sql .= " AND ";
            } else {
                $sql = ' WHERE ';
                $first = false;
            }
            $sql .= "$field = :$field";
            $params[$field] = $value;
        }
        return array(
            'where'  => $sql,
            'params' => $params
        );
    }

    /**
     * MysqlModel::query
     *
     * Query DB object
     *
     * @param string     $queryString Query string
     * @param array|null $params      Optional: Parameters to apply to query
     * @param integer    $fetchType   Optional: Type of response
     * @param boolean    $reduce      Optional: reduce result : raw result
     *
     * @return array|string|integer Result of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function query($queryString, $params=null, $fetchType=\PDO::FETCH_ASSOC, $reduce=true)
    {
        $stmt = $this->connection->prepare($queryString);
        if (!is_null($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        return $reduce
            ? $this->reduceResult($stmt->fetch($fetchType))
            : $stmt->fetch($fetchType);
    }

    /**
     * MysqlModel::getAll
     *
     * Return all values based on collection and where
     *
     * @param string|array $collection Collection (table) to search for key
     * @param string|array $where      Optional: Where clause
     *
     * @return array|string|integer Result of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function getAll($collection, $where=null)
    {
        return $this->get('*', $collection, $where);
    }

    /**
     * MysqlModel::set
     *
     * Set values in DB
     *
     * @param array  $assignments Name of value to set
     * @param string $collection  Name of collection to set $key to $value
     * @param string $where       Optional: Where clause
     *
     * @return boolean|integer Result of query
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function set($assignments, $collection, $where=null)
    {
        $key = array_keys($assignments);
        $fields = array();
        foreach ($key as $name) {
            array_push($fields, ':'.$name);
        }
        $sql = "INSERT INTO $collection (".MysqlModel::stringify($key, true, '`').") VALUES(".implode(',', $fields).")";

        if (!is_null($where)) {
            // Initializes $where and $params
            extract(
                $this->buildWhere($where)
            );
            $sql .= $where;
        } else {
            $params = array();
        }
        return $this->query($sql, array_merge($assignments, $params));
    }

    /**
     * MysqlModel::sanitize
     *
     * Sanitizes inputs for queries
     *
     * @param string $queryString Raw query string
     *
     * @return string Sanitized string
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function sanitize($queryString)
    {
        /* PDO requires no sanitization */
        return $queryString;
    }

    /**
     * MysqlModel::reduceResult
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
     * MysqlModel::stringify
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
    function stringify($array, $force = false, $quotation="'")
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
