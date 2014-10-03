<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MysqlModelSpec extends ObjectBehavior
{
    function let(\PDO $connectionMock)
    {
        $this->beConstructedWith($connectionMock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\MysqlModel');
    }

    function it_should_perform_PDO_queries(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock->prepare(
            "SELECT `user_name` FROM users WHERE `userid` = :userid"
        )->willReturn($stmtMock);

        $stmtMock->execute(
            array(
                'userid' => 1
            )
        )->willReturn(true);

        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)
            ->willReturn(
                array('user_name' => 'seagoj')
            );

        $this->get('user_name', 'users', array('userid' => 1))
            ->shouldReturn(['user_name' => 'seagoj']);
    }

    function it_should_return_multiple_values(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock->prepare(
            "SELECT `user_name`,`last_name` FROM users WHERE `userid` = :userid"
        )->willReturn($stmtMock);

        $stmtMock->execute(
            array(
                'userid' => 1
            )
        )->willReturn(true);

        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)
            ->willReturn(
                array(
                    'user_name' => 'seagoj',
                    'last_name' => 'Seago'
                )
            );

        $this->get(array('user_name', 'last_name'), 'users', array('userid' => 1))
            ->shouldReturn(array('user_name' => 'seagoj', 'last_name' => 'Seago'));
    }

    function it_should_set_a_value(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $sql = "INSERT INTO users (`user_name`,`last_name`) VALUES (:user_name,:last_name) ON DUPLICATE KEY UPDATE `user_name`=VALUES(`user_name`),`last_name`=VALUES(`last_name`)";
        $connectionMock->prepare(
            $sql
        )->willReturn($stmtMock);

        $stmtMock->execute(
            array(
                'user_name' => 'seagoj',
                'last_name' => 'Seago'
            )
        )->willReturn(true);

        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)
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
            array(
                'userid' => 1000
            )
        );
    }
}
