<?php namespace Devtools;

use SqlStatement;

class Query
{
    public $sql;
    public $params;

    public function __construct(SqlStatement $sql, $params = null)
    {
        if (empty($params)) {
            $params = null;
        }

        $this->sql    = $sql->build();
        $this->params = $params;
    }
}
