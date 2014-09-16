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
class MysqlModel extends Model
{
    /**
     * __construct
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
            $connection = $this->connect(
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
     * connect
     *
     * Establish connection if one is not passed
     *
     * @return void
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public function connect($options = null)
    {
        if (!is_null($options)) {
            $this->options = array_merge($this->options, $options);
        }
        $keys = array_keys($this->options);
        if (in_array('type',  $keys)
            && in_array('host', $keys)
            && in_array('db', $keys)
            && in_array('username', $keys)
            && in_array('password', $keys)
        ) {
            return  new \PDO(
                sprintf(
                    "%s:host=%s;dbname=%s",
                    $this->options['type'],
                    $this->options['host'],
                    $this->options['db']
                ),
                $this->options['username'],
                $this->options['password']
            );
        } else {
            throw new \Exception('Invalid connection options.');
        }
    }

    /**
     * get
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
     * query
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
        var_dump($fetchType);
        return $reduce
            ? $this->reduceResult($stmt->fetchAll($fetchType))
            : $stmt->fetchAll($fetchType);
    }

    /**
     * getAll
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
     * set
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
     * sanitize
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
}
