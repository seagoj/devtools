<?php
class QueryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers \Devtools\Query::__construct
     **/
    public function testQuery()
    {
        $this->assertInstanceOf("\Devtools\Query", new \Devtools\Query());
    }

    /**
     * @covers \Devtools\Query::push
     * @covers \Devtools\Query::countRows
     * @covers \Devtools\Query::extractColumns
     **/
    public function testPushFirst()
    {
        $row = ["col1" => "data1","col2" => "data2"];
        $colNames = [];
        foreach ($row as $col => $data) {
            array_push($colNames, $col);
        }
        
        $q = new \Devtools\Query();
        $q->push($row);
        //  $this->assertEquals($colNames, $q->colNames);
        //  $this->assertEquals([$row], $q->data);
    }
}
