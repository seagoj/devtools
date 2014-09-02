<?php

namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MysqlModelSpec extends ObjectBehavior
{
    function let(\PDO $connection)
    {
        $this->beConstructedWith($connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\MysqlModel');
    }

    function it_should_santize_input()
    {
        $this->sanitize(
            "SELECT * FROM users WHERE 1; DROP TABLE;"
        )->shouldReturn(
            "SELECT * FROM users WHERE 1; DROP TABLE;"
        );
    }

    function it_should_perform_PDO_queries(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->prepare(
            "SELECT `user_name` FROM users WHERE userid = :userid"
        )->willReturn($stmt);

        $stmt->execute(
            array(
                'userid' => 1
            )
        )->willReturn(true);

        $stmt->fetch(\PDO::FETCH_ASSOC)
            ->willReturn(
                array('user_name' => 'seagoj')
            );

        $this->get('user_name', 'users', array('userid' => 1))
            ->shouldReturn('seagoj');
    }

    function it_should_return_multiple_values(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->prepare(
            "SELECT `user_name`,`last_name` FROM users WHERE userid = :userid"
        )->willReturn($stmt);

        $stmt->execute(
            array(
                'userid' => 1
            )
        )->willReturn(true);

        $stmt->fetch(\PDO::FETCH_ASSOC)
            ->willReturn(
                array(
                    'user_name' => 'seagoj',
                    'last_name' => 'Seago'
                )
            );

        $this->get(array('user_name', 'last_name'), 'users', array('userid' => 1))
            ->shouldReturn(array('user_name' => 'seagoj', 'last_name' => 'Seago'));
    }

    function it_should_set_a_value(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->prepare(
            "INSERT INTO users (`user_name`,`last_name`) VALUES(:user_name,:last_name)"
        )->willReturn($stmt);

        $stmt->execute(
            array(
                'user_name' => 'seagoj',
                'last_name' => 'Seago'
            )
        )->willReturn(true);

        $stmt->fetch(\PDO::FETCH_ASSOC)
            ->willReturn(
                array(
                    'userid' => 1000
                )
            );

        $this->set(
            array(
                'user_name' => 'seagoj',
                'last_name' => 'Seago'
            ),
            'users'
        )->shouldReturn(
            1000
        );
    }
}