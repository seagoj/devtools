<?php namespace Devtools;

abstract class PDOModel extends Model
{
    public function __construct(\PDO $connection)
    {
        $this->connection = $connection;
    }

    public static function connect($parameters)
    {
        $keys = array_keys($parameters);
        if (!in_array('type',  $keys)
            || !in_array('host', $keys)
            || !in_array('db', $keys)
            || !in_array('username', $keys)
            || !in_array('password', $keys)
        ) {
            throw new \Exception('Invalid connection options.');
        }

        switch($parameters['type']) {
        case 'mysql':
            $connectionStr = '%s:host=%s;dbname=%s';
            break;
        case 'firebird':
            $connectionStr = '%s:dbname=%s:%s';
            break;
        }

        return new \PDO(
            sprintf(
                $connectionStr,
                $parameters['type'],
                $parameters['host'],
                $parameters['db']
            ),
            $parameters['username'],
            $parameters['password']
        );
    }

    public function get($key, $collection, Array $where=null)
    {
       $sql = "SELECT ".($key==='*' ? '*' : self::stringify($key, true, '`'))
           ." FROM $collection";

       if (!is_null($where)) {
           extract(
               $this->buildWhere($where)
           );
           $sql .= $where;
       } else {
           $params = null;
       }

       return $this->query($sql, $params);
    }

    protected function buildWhere($where)
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
            $sql .= "`$field` = :$field";
            $params[$field] = $value;
        }
        return array(
            'where'  => $sql,
            'params' => $params
        );
    }

    public function query($queryString, $params = null, $reduce=false, $fetchType=\PDO::FETCH_ASSOC)
    {
        $queryString = $this->stripWhitespace($queryString);

        if (strpos(strtoupper($queryString), 'IN ') && !is_null($params)) {
            $this->fixInClause($queryString, $params);
        }

        $stmt = $this->connection->prepare($queryString);
        if (!is_null($params)) {
            $stmt->execute($params);
        } else {
            $stmt->execute();
        }
        $data = $stmt->fetchAll($fetchType);
        if (empty($data) && strpos(strtoupper($queryString), 'INSERT ') !== false) {
            $data = array('insert_id' => $this->connection->lastInsertId());
        }
        return $reduce
            ? $this->reduceResult($data)
            : $data;
    }

    private function fixInClause(&$queryString, &$params)
    {
       foreach ($params as $field => $value) {
           if (is_array($value)) {
               $queryString = str_replace(':'.$field, self::stringify($value), $queryString);
               unset($params[$field]);
           }
       }
    }

    public function isConnected()
    {
        return  isset($this->connection) && !empty($this->connection);
    }

    public function getAll($collection, $where=null)
    {
       return $this->get('*', $collection, $where);
    }
}
