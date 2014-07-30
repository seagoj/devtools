<?php
class FirebirdModelTest extends PHPUnit_Framework_TestCase
{
    public function testConnection()
    {
        $firebird = new \Devtools\FirebirdModel(array());
        $this->assertInstanceOf('Devtools\FirebirdModel', $firebird);
    }

    public function testQuery()
    {
        $firebird = new \Devtools\FirebirdModel(array());
        $this->assertEquals(
            array(
                "IMAGE_ID" => 1,
                "IMAGE_NAME" => "FirstImage.jpg"
            ),
            $firebird->query("select * from IMAGE where IMAGE_ID=1", true)
        );
        $this->assertEquals(
            array(
                array(
                    "IMAGE_ID" => 1,
                    "IMAGE_NAME" => "FirstImage.jpg"
                )
            ),
            $firebird->query("select * from IMAGE where IMAGE_ID=1")
        );
    }
}

function ibase_pconnect($connectionString, $user, $pass)
{
    if ($connectionString === 'HOST:C:\TOMORROW\NJ\CMPDWIN.PKF'
        && $user === 'DBA'
        && $pass === 'PASSWORD'
    ) {
        return new stdClass();
    } else {
        return false;
    }
}

function ibase_query($connection, $sql)
{
    $result = array();
    switch($sql) {
    case "select * from IMAGE where IMAGE_ID=1":
        $result = array(
            array(
                "IMAGE_ID" => 1,
                "IMAGE_NAME" => "FirstImage.jpg"
            )
        );
        break;
    default:
        var_dump($sql);
        break;
    }
    return $result;
}

function ibase_fetch_assoc($qry, $type)
{
    static $count;

    if (empty($count)) {
        $count = 0;
    }
    if (count($qry)>$count && isset($qry[$count])) {
        return $qry[$count++];
    } else {
        $count =0;
        return false;
    }
}

function ibase_free_result(&$qry)
{
    unset($qry);
}

function ibase_errmsg()
{
    return "error";
}
