<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FirebirdModelSpec extends ObjectBehavior
{
    function let(\PDO $connectionMock)
    {
        $this->beConstructedWith($connectionMock);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\FirebirdModel');
    }

    function it_connects_when_passed_a_model_object()
    {
        $this->shouldBeConnected();
    }

    function it_gets_a_single_value_as_string(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock
            ->prepare("SELECT `user_name` FROM users WHERE `user_id` = :user_id")
            ->willReturn($stmtMock);
        $stmtMock->execute(['user_id' => 1])->willReturn(true);
        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)->willReturn([['user_name' => 'seagoj']]);

        $this->get('user_name', 'users', array('user_id' => 1))
            ->shouldReturn([['user_name' => 'seagoj']]);
    }

    function it_gets_multiple_values_as_array(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock
            ->prepare("SELECT `user_name`,`last_name` FROM users WHERE `user_id` = :user_id")
            ->willReturn($stmtMock);
        $stmtMock->execute(['user_id' => 1])->willReturn(true);
        $stmtMock
            ->fetchAll(\PDO::FETCH_ASSOC)
            ->willReturn([['user_name' => 'seagoj', 'last_name' => 'Seago']]);

        $this->get(
            ['user_name', 'last_name'],
            'users',
            ['user_id' => 1]
        )->shouldReturn(
            [['user_name' => 'seagoj', 'last_name' => 'Seago']]
        );
    }

    function it_sets_values_based_on_array(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock
            ->prepare("INSERT INTO users (`first_name`) VALUES (:first_name) WHERE `user_id` = :user_id")
            ->willReturn($stmtMock);
        $stmtMock->execute(['first_name' => 'Jeremy', 'user_id' => 1])->willReturn(true);
        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)->willReturn([['insert_id' => 1000]]);

        $this->set(
            ['first_name' => 'Jeremy'],
            'users',
            ['user_id' => 1]
        )->shouldReturn(1000);
    }

    function it_returns_all_values_from_a_collection(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock->prepare('SELECT * FROM users')
            ->willReturn($stmtMock);
        $stmtMock ->execute()->willReturn(true);
        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 => [
                    'user_id' => 1,
                    'user_name' => 'seagoj',
                    'first_name' => 'Jeremy',
                    'last_name' => 'Seago'
                ],
                1 => [
                    'user_id' => 2,
                    'user_name' => 'jsmith',
                    'first_name' => 'John',
                    'last_name' => 'Smith'
                ]
            ]
        );

        $this->getAll(
            'users'
        )->shouldReturn(
            [
                0 => [
                    'user_id' => 1,
                    'user_name' => 'seagoj',
                    'first_name' => 'Jeremy',
                    'last_name' => 'Seago'
                ],
                1 => [
                    'user_id' => 2,
                    'user_name' => 'jsmith',
                    'first_name' => 'John',
                    'last_name' => 'Smith'
                ]
            ]
        );
    }

    function it_conditionally_returns_all_from_database(\PDO $connectionMock, \PDOStatement $stmtMock)
    {
        $connectionMock->prepare('SELECT * FROM users WHERE `user_id` = :user_id')
            ->willReturn($stmtMock);
        $stmtMock->execute(['user_id' => 1])->willReturn(true);
        $stmtMock->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [[
            'user_id' => 1,
            'user_name' => 'seagoj',
            'first_name' => 'Jeremy',
            'last_name' => 'Seago'
            ]]
        );

        $this->getAll(
            'users',
            array(
                'user_id' => 1
            )
        )->shouldReturn(
            [[
                'user_id' => 1,
                'user_name' => 'seagoj',
                'first_name' => 'Jeremy',
                'last_name' => 'Seago'
            ]]
        );
    }
}
