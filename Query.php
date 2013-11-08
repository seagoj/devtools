<?php

namespace Devtools;

class Query
{
    public $data;
    public $colNames;
    public $rowCount;

    public function __construct($data=array(), $options=array())
    {
        $this->colNames = array();
        $this->data = $data;
        $this->countRows();
        $this->extractColumns();
    }

    public function push($row, $debug)
    {
        
        $first = empty($this->data);
       
        foreach($row as $key=>$value) {
            $this->data[$key] = $value;
            $this->rowCount++;
        }

        if ($first) $this->extractColumns();
    }

    private function countRows()
    {
        $this->rowCount = count($this->data);
    }

    private function extractColumns()
    {
        if(!empty($this->data)) {
            foreach($this->data as $key=>$value) {
                array_push($this->colNames, $key);
            }
        }
    }
}
