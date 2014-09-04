<?php
namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FirebirdModelSpec extends ObjectBehavior
{
    function let(\PDO $connection, \PDOStatement $stmt1,
        \PDOStatement $stmt2, \PDOStatement $stmt3,
        \PDOStatement $stmt4, \PDOStatement $stmt5
    ) {
        /* MOCK: it_gets_a_single_value_as_string */
        $stmt1->execute(['user_id' => 1])->willReturn(true);
        $stmt1->fetchAll(\PDO::FETCH_ASSOC)->willReturn([['user_name' => 'seagoj']]);
        $connection
            ->prepare("select `user_name` from users where `user_id`=:user_id")
            ->willReturn($stmt1);

        /* MOCK: it_gets_multiple_values_as_array */
        $stmt2->execute(['user_id' => 1])->willReturn(true);
        $stmt2
            ->fetchAll(\PDO::FETCH_ASSOC)
            ->willReturn([['user_name' => 'seagoj', 'last_name' => 'Seago']]);
        $connection
            ->prepare("select `user_name`,`last_name` from users where `user_id`=:user_id")
            ->willReturn($stmt2);

        /* MOCK: it_sets_values_based_on_array */
        $stmt3->execute(['user_id' => 1])->willReturn(true);
        $stmt3->fetchAll(\PDO::FETCH_ASSOC)->willReturn([['insert_id' => 1000]]);
        $connection
            ->prepare("insert into users (`first_name`) values ('Jeremy') where `user_id`=:user_id")
            ->willReturn($stmt3);

        /* MOCK: it_returns_all_values_from_a_collection */
        $stmt4 ->execute()->willReturn(true);
        $stmt4->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
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
        $connection->prepare('select * from users')
            ->willReturn($stmt4);

        /* MOCK: it_conditionally_returns_all_from_database */
        $stmt5->execute(['user_id' => 1])->willReturn(true);
        $stmt5->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [[
            'user_id' => 1,
            'user_name' => 'seagoj',
            'first_name' => 'Jeremy',
            'last_name' => 'Seago'
            ]]
        );
        $connection->prepare('select * from users where `user_id`=:user_id')
            ->willReturn($stmt5);

        $this->beConstructedWith($connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\FirebirdModel');
    }

    function it_connects_when_passed_a_model_object()
    {
        $this->shouldBeConnected();
    }

    function it_gets_a_single_value_as_string()
    {
        $this->get('user_name', 'users', array('user_id' => 1))
            ->shouldReturn('seagoj');
    }

    function it_gets_multiple_values_as_array()
    {
        $this->get(
            ['user_name','last_name'],
            'users',
            ['user_id' => 1]
        )->shouldReturn(
            ['user_name' => 'seagoj', 'last_name' => 'Seago']
        );
    }

    function it_sets_values_based_on_array()
    {
        $this->set(
            ['first_name' => 'Jeremy'],
            'users',
            ['user_id' => 1]
        )->shouldReturn(1000);
    }

    function it_returns_all_values_from_a_collection()
    {
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

    function it_conditionally_returns_all_from_database()
    {
        $this->getAll(
            'users',
            array(
                'user_id' => 1
            )
        )->shouldReturn(
            array(
                'user_id' => 1,
                'user_name' => 'seagoj',
                'first_name' => 'Jeremy',
                'last_name' => 'Seago'
            )
        );
    }
}
