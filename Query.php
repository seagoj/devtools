<?php namespace Devtools;

class Query
{
    public $sql;
    public $params;

    public function __construct($sql, $params = null)
    {
        if (empty($params)) {
            $params = null;
        }

        $this->sql    = $sql;
        $this->params = $params;
    }
}
