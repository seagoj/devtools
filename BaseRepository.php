<?php namespace Devtools;

abstract class BaseRepository
{
    protected $connection;
    protected $log;
    protected $table;
    protected $fillable;
    protected $required;
    protected $primaryKey;
    protected $data;
    protected $count;
    protected $query;

    public function __get($property)
    {
        return !isset($this->data) || in_array($property, array_keys($this->data))
            ? $this->data[$property]
            : null;
    }

    public function __set($property, $value)
    {
        $this->data[$property] = $value;
    }

    public function reset()
    {
        $this->query = '';
        $this->data = array();
        $this->params = null;
    }

    public function find($id)
    {
        $this->loadQueryStringIfEmpty();
        return $this->findBy($id);
    }

    public function findBy($filter = null)
    {
        if (is_null($filter)) {
            return $this->all();
        }

        if (is_string($filter) || is_integer($filter)) {
            $filter = array($this->primaryKey, '=', $filter);
        }

        $this->apply(
            $this->all()->where($filter)->get()
        );

        return $this;
    }

    public function findOrFail($filter = null)
    {
        return $this->findBy($filter)->orFail();
    }

    public function orFail()
    {
        $result = $this->get();
        if (empty($result) || !$result) {
            throw new \Exception('Query failed or result is empty.');
        }
        return $result;
    }

    public function count()
    {
        $this->count = count($this->get());
        return $this->count;
    }

    public static function stringify($array, $force = false, $quotation="'")
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

    public static function stripWhitespace($dirty)
    {
        return preg_replace("/[ \\t\\n]+/u", " ", $dirty);
    }

    public static function reduceResult($result)
    {
        if (is_array($result) && (count($result) == 1)) {
            reset($result);
            return self::reduceResult($result[key($result)]);
        } else {
            return $result;
        }
    }

    protected function apply(Array $values)
    {
        foreach ($values as $field => $value) {
            $this->data[$field]  = $value;
        }
    }

    private function loadQueryStringIfEmpty()
    {
        if (empty($this->queryString)) {
            $this->all();
        }
    }
}
