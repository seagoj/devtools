<?php
define('IBASE_TEXT', 'IBASE_TEXT');

function ibase_query($connection, $sql)
{
    if ($connection !== "TestResource") {
        return false;
    }

    switch(stripWhitespace($sql)) {
    case "select `user_name` from users where `user_id`=1":
        return 'TEST1';
    case "select `user_name`,`last_name` from users where `user_id`=1":
        return 'TEST2';
    case "insert into users (`first_name`) values ('Jeremy') where `user_id`=1":
        return 'TEST3';
    case "select * from users":
        return "TEST4";
    case "select * from users where `user_id`=1";
        return "TEST5";
    default:
        var_dump(stripWhitespace($sql));
        var_dump(__METHOD__."Not Defined");
        return false;
    }
}

function ibase_fetch_assoc($qry, $type)
{
    static $called = array();
    if ($type !== IBASE_TEXT) {
        return false;
    }

    /* @todo account for partial return of array */
    switch($qry) {
    case 'TEST1':
        if (in_array($qry, $called)) {
            return false;
        }
        array_push($called, $qry);
        return array("user_name" => "seagoj");
    case 'TEST2':
        if (in_array($qry, $called)) {
            return false;
        }
        array_push($called, $qry);
        return array(
            "user_name" => "seagoj",
            "last_name" => "Seago"
        );
    case 'TEST3':
        if (in_array($qry, $called)) {
            return false;
        }
        array_push($called, $qry);
        return array('insert_id' => 1000);
    case 'TEST4':
        if (in_array($qry, $called)) {
            return false;
        }
        array_push($called, $qry);
        return array(
            0 => array(
                'user_id' => 1,
                'user_name' => 'seagoj',
                'first_name' => 'Jeremy',
                'last_name' => 'Seago'
            ),
            1 => array(
                'user_id' => 2,
                'user_name' => 'jsmith',
                'first_name' => 'John',
                'last_name' => 'Smith'
            )
        );
    case 'TEST5':
        if (in_array($qry, $called)) {
            return false;
        }
        array_push($called, $qry);
        return array(
            'user_id' => 1,
            'user_name' => 'seagoj',
            'first_name' => 'Jeremy',
            'last_name' => 'Seago'
        );
    default:
        var_dump(stripWhitespace($qry));
        var_dump(__METHOD__."Not Defined");
        return false;
    }
}


function ibase_free_result()
{
    return true;
}

/* @todo Extract to reusable class */
function stripWhitespace($dirty)
{
    return preg_replace("/[ \\t\\n]+/u", " ", $dirty);
}
