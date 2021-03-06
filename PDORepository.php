<?php namespace Devtools;

abstract class PDORepository extends BaseRepository implements Repository
{
    protected $params;
    protected $position;

    public function __construct(\PDO $connection, Log $log)
    {
        $this->connection = $connection;
        $this->log = $log;
        $this->params = null;
        $this->position = 1;
        $this->count = null;
    }

    public function reset()
    {
        parent::reset();
        $this->position = 1;
        $this->count = null;
    }

    public function get()
    {
        $result = $this->query(
            $this->getQueryString(),
            $this->params,
            true
        );

        if (is_array($result)) {
            $this->apply($result);
        }
        return $result;
    }

    public function all()
    {
        $this->query = "SELECT * FROM {$this->table}";
        return $this;
    }

    public function delete()
    {
        $this->loadIfNotLoaded();
        $this->checkForData();
        $this->checkDataForPrimaryKey($this->data);

        $sql = "DELETE FROM {$this->table} WHERE `{$this->primaryKey}` = :{$this->primaryKey}";
        return $this->query($sql, $this->params);
    }

    public function query($query, $params = null,
        $reduce=false, $fetchType=\PDO::FETCH_ASSOC
    ) {
        $query = $this->stripWhitespace($query);
        if (strpos(strtoupper($query), 'IN ')
            && !is_null($params)
        ) {
            $this->fixInClause($query, $params);
        }

        if ('firebird' == $this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            $this->log->write('===== FIREBIRD =====');
            $this->log->write($query);
            $this->log->write($params);
        }

        $stmt = $this->connection->prepare($query);
        $executionResult = !is_null($params)
            ? $stmt->execute($params)
            : $stmt->execute();
        $data = $stmt->fetchAll($fetchType);

        $connectionError = $this->connection->errorInfo();
        if (!is_null($connectionError[1])) {
            $this->log->write('==========');
            $this->log->write($connectionError);
            $this->log->write($query);
            $this->log->write($params);
            $this->log->write('==========');
        }

        $statementError  = $stmt->errorInfo();
        if (!is_null($statementError[1])) {
            $this->log->write('==========');
            $this->log->write($statementError);
            $this->log->write($query);
            $this->log->write($params);
            $this->log->write('==========');
        }

        $this->prepareResponseData($data, $executionResult);

        return $reduce
            ? self::reduceResult($data)
            : $data;
    }

    public function where(Array $clause, $operand = null)
    {
        $params = array();
        $isInitalWhereCall = is_null($operand);
        $isLogicalOperand = in_array(strtoupper($operand), array('AND', 'OR'));

        if ($isInitalWhereCall) {
            $clause = $this->wrapInArrayIfNotAssoc($clause);
        }

        foreach ($clause as $key => $value) {
            if (is_array($value)) {
                list($clauseRecurse, $paramRecurse) = $this->where($value, $key);
                $clause[$key] = $clauseRecurse;
                $params = array_merge($params, $paramRecurse);
            }
        }

        if ($isInitalWhereCall) {
            return $this->whereRaw(array_pop($clause), $params);
        }

        if ($isLogicalOperand) {
            $operand = " $operand ";
        } else {
            $operand = ' ';
            $this->prepareBinding($clause, $params);
        }

        return array(
            implode($operand, $clause),
            $params
        );
    }

    public function whereRaw($clause, Array $params)
    {
        if (empty($this->query)) {
            $this->all();
        }
        $this->query .= " WHERE {$clause}";
        $this->params = $params;
        return $this;
    }

    public function take($rowsInBatch)
    {
        if (is_null($this->count)) {
            $this->count();
        }

        if ($this->position > $this->count) {
            return;
        }

        if ($this->position === $this->count) {
            $rowsInBatch = 1;
        } else if ($this->position+$rowsInBatch > $this->count) {
            $rowsInBatch = $this->count - $this->position;
        }

        $count = $this->position + $rowsInBatch - 1;

        $result = $this->query(
            "{$this->query} LIMIT {$this->position},{$count}",
            $this->params,
            true
        );

        $this->position += $rowsInBatch;
        return $result;
    }

    public function isConnected()
    {
        return  isset($this->connection) && !empty($this->connection);
    }

    public function save()
    {
        return $this->update($this->data);
    }

    public function update(Array $values)
    {
        $this->checkDataForPrimaryKey($values);

        $sql = "UPDATE {$this->table} SET ";
        $first = true;
        foreach (array_keys($values) as $key) {
            if ($key !== $this->primaryKey) {
                if (!$first) {
                    $sql .= ',';
                }
                $sql .= ($key.'=:'.$key);
                $first = false;
            }
        }
        $sql .= " WHERE {$this->primaryKey}=:{$this->primaryKey}";
        $result = $this->query($sql, $values, true);
        $this->find($this->data[$this->primaryKey])->get();
        return $result;
    }

    public function create(Array $userValues)
    {
        $this->checkDataForRequiredFields($userValues);

        $fields = array();
        $bindings = array();
        foreach (array_keys($userValues) as $key) {
            if ($key !== $this->primaryKey) {
                array_push($fields, $key);
                array_push($bindings, ':'.$key);
            }
        }

        $fields = implode(',', $fields);
        $bindings = implode(',', $bindings);

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$bindings})";
        return $this->query($sql, $userValues, true);
    }

    public function first()
    {
        $this->query .= " LIMIT 1";
        return $this;
    }

    private function loadIfNotLoaded()
    {
        if (!isset($this->data) && !empty($this->query)) {
            $this->get();
        }
    }

    private function checkForData()
    {
        if (!isset($this->data)) {
            throw new \Exception('Repository contains no data.');
        }
    }

    private function checkDataForRequiredFields($data)
    {
        foreach ($this->required as $requiredField) {
            if (!in_array($requiredField, array_keys($data))) {
                throw new \Exception($requiredField.' is not a value in creation array.');
            }
        }
    }

    private function checkDataForPrimaryKey(&$data)
    {
        if (!in_array($this->primaryKey, array_keys($data)) && $this->data[$this->primaryKey]) {
            $data[$this->primaryKey] = $this->data[$this->primaryKey];
        }

        if (!in_array($this->primaryKey, array_keys($data))) {
            throw new \Exception('Primary key is not in value set.');
        }
    }

    private function prepareResponseData(&$data, $executionResult)
    {
        if (empty($data)) {
            switch($this->connection->getAttribute(\PDO::ATTR_DRIVER_NAME)) {
            case 'mssql':
            case 'dblib':
            case 'firebird':
                break;
            default:
                $isInsertStatement = $executionResult
                    && ($lastInsertId = $this->connection->lastInsertId()) != 0;

                $data = $isInsertStatement
                    ?  array('insert_id' => $lastInsertId)
                    :  $executionResult;
                break;
            }
        }
    }

    private function fixInClause(&$query, &$params)
    {
        foreach ($params as $field => $value) {
            if (is_array($value)) {
                $query = str_replace(':'.$field, self::stringify($value), $query);
                unset($params[$field]);
            }
        }
    }

    private function getQueryString()
    {
        return $this->stripWhitespace(
            $this->query
        );
    }

    private function prepareBinding(&$clause, &$params)
    {
        $params[$clause[0]] = array_pop($clause);
        $binding = strtoupper($clause[1]) === 'IN'
            ? "(:{$clause[0]})"
            : ":{$clause[0]}";
        array_push($clause, $binding);
        $clause[0] = "`{$clause[0]}`";
    }

    private function wrapInArrayIfNotAssoc($clause)
    {
        $encapsulate = false;
        foreach ($clause as $key => $value) {
            $encapsulate = $encapsulate || (is_numeric($key) && !is_array($value));
        }
        return $encapsulate ? array($clause) : $clause;
    }
}
