<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PDOAdapterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\PDOAdapter');
    }


    function it_should_validate_connection_parameters()
    {
        $this->shouldThrow(
            new \Exception('Invalid connection options.')
        )->duringConnect([]);
    }

    function it_should_build_a_connection_string_based_on_type()
    {
        $parametersMysql =  [
            'type' => 'mysql',
            'host' => 'localhost',
            'db' => 'db'
        ];
        $this::getConnectionString($parametersMysql)->shouldReturn(
            'mysql:host=localhost;dbname=db'
        );

        $parametersFirebird =  [
            'type' => 'firebird',
            'host' => 'localhost',
            'db' => 'db'
        ];
        $this->getConnectionString($parametersFirebird)->shouldReturn(
            'firebird:dbname=localhost:db'
        );
    }

    /* function it_should_create_a_pdo_connection() */
    /* { */
    /*     /1* Exception thrown on attempted connection to non-existent datastore *1/ */
    /*     $this->shouldThrow( */
    /*         new \PDOException("SQLSTATE[HY000] [2002] No such file or directory", 2002) */
    /*     )->duringConnect( */
    /*         [ */
    /*             'type'     => 'mysql', */
    /*             'host'     => 'localhost', */
    /*             'db'       => 'db', */
    /*             'username' => 'username', */
    /*             'password' => 'password' */
    /*         ] */
    /*     ); */
    /* } */
}
