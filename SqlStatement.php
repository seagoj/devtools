<?php namespace Devtools;

class SqlStatement
{
    public $verb    = 'SELECT';
    public $columns = '*';
    public $values  = array();
    public $table   = '';
    public $join    = array();
    public $where   = array();
    public $order   = array();
    public $limit   = '';

    public function build()
    {
        $join = implode('', $this->join);

        $where = !empty($this->where)
            ? implode('', $this->where)
            : '';

        $order = !empty($this->order)
            ? implode(',', $this->order)
            : '';

        $limit = !empty($this->limit)
            ? $this->limit
            : '';

        switch ($this->verb) {
        case 'SELECT':
            return trim(
                Format::stripWhitespace(
                    "SELECT * FROM {$this->table} {$join} {$where} {$order} {$limit}"
                )
            );
        }
    }
}
