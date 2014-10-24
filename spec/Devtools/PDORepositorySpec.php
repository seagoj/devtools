<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class PDORepositorySpec extends ObjectBehavior
{
    function let(\PDO $connection)
    {
        $this->beAnInstanceOf(
            'spec\Devtools\TestPDORepository'
        );
        $this->beConstructedWith($connection);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(
            'spec\Devtools\TestPDORepository'
        );
    }

    function it_returns_all_models(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test")
            ->willReturn($stmt);
        $stmt->execute()->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ],
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );
        $this->all()->shouldReturn($this);
        $this->all()->get()->shouldReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ],
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );
    }

    function it_returns_a_model_from_where_object(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test WHERE `testid` IN (:testid)")->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 =>
                    [
                        'testid'    => 1,
                        'testvalue' => 'Test 1'
                    ]
            ]
        );

        /* $this->where(['testid', '=', 1]) */
        /*     ->get()->shouldReturn( */
        /*         [ */
        /*             'testid'    => 1, */
        /*             'testvalue' => 'Test 1' */
        /*         ] */
        /*     ); */

        $this->where(['testid', 'IN', '1,2,3'])
            ->get()->shouldReturn(
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            );
    }

    function it_returns_a_model_from_raw_where(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test WHERE `testid` = :testid")->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 =>
                    [
                        'testid'    => 1,
                        'testvalue' => 'Test 1'
                    ]
            ]
        );

        $this->whereRaw('`testid` = :testid', ['testid' => 1])
            ->get()->shouldReturn(
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            );
    }

    function it_returns_a_model_by_filter(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test WHERE `testid` = :testid")->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 =>
                    [
                        'testid'    => 1,
                        'testvalue' => 'Test 1'
                    ]
            ]
        );

        $this->findBy(['testid', '=', 1])->get()->shouldReturn(
            [
                'testid'    => 1,
                'testvalue' => 'Test 1'
            ]
        );
    }

    function it_returns_a_model_by_primary_key(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test WHERE `testid` = :testid")->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 =>
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            ]
        );

        $this->find(1)->get()->shouldReturn(
            [
                'testid'    => 1,
                'testvalue' => 'Test 1'
            ]
        );
    }

    function it_returns_the_first_model_from_selection(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test LIMIT 1")
            ->willReturn($stmt);
        $stmt->execute()->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            ]
        );

        $this->all()->shouldReturn($this);
        $this->all()->first()->get()->shouldReturn(
            [
                'testid'    => 1,
                'testvalue' => 'Test 1'
            ]
        );
    }

    function it_returns_the_a_subset_of_the_model(\PDO $connection, \PDOStatement $stmt,
        \PDOStatement $stmt2, \PDOStatement $stmt3
    ) {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test")
            ->willReturn($stmt);
        $stmt->execute()->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ],
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );

        $connection->prepare("SELECT * FROM test LIMIT 1,1")
            ->willReturn($stmt2);
        $stmt2->execute()->willReturn(true);
        $stmt2->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            ]
        );

        $connection->prepare("SELECT * FROM test LIMIT 2,2")
            ->willReturn($stmt3);
        $stmt3->execute()->willReturn(true);
        $stmt3->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );

        $this->all()->shouldReturn($this);
        $this->take(1)->shouldReturn(
            [
                'testid'    => 1,
                'testvalue' => 'Test 1'
            ]
        );
        $this->take(1)->shouldReturn(
            [
                'testid'    => 2,
                'testvalue' => 'Test 2'
            ]
        );
    }

    function it_returns_count_of_rows_returned(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test")
            ->willReturn($stmt);
        $stmt->execute()->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ],
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );

        $this->all()->count()->shouldReturn(2);
    }

    function it_writes_the_current_values_to_datastore(\PDO $connection, \PDOStatement $stmt, \PDOStatement $stmt2)
    {
        $connection->lastInsertId()->willReturn(0);
        $connection->prepare("SELECT * FROM test WHERE `testid` = :testid")->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                0 =>
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ]
            ]
        );

        $connection->prepare('UPDATE test SET testvalue=:testvalue WHERE testid=:testid')->willReturn($stmt2);
        $stmt2->execute(['testid'=>1, 'testvalue'=>'Updated Test Value'])->willReturn(true);
        $stmt2->fetchAll(\PDO::FETCH_ASSOC)->willReturn();

        $this->find(1)->get();
        $this->testvalue = 'Updated Test Value';
        $this->testid->shouldReturn(1);
        $this->testvalue->shouldReturn('Updated Test Value');
        $this->save()->shouldReturn(true);
    }

    function it_should_throw_exception_is_user_is_created_without_required_field()
    {
        $this->shouldThrow(new \Exception('testvalue is not a value in creation array.'))->duringCreate([
            'notatestvalue' => 'Not A Test Value'
        ]);
    }

    function it_creates_a_new_model_from_array_with_primary_key(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->lastInsertId()->willReturn(10);
        $connection->prepare('INSERT INTO test (testvalue) VALUES (:testvalue)')->willReturn($stmt);
        $stmt->execute(['testvalue' => 'New Test Value'])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn();

        $this->create(['testvalue' => 'New Test Value'])->shouldReturn(10);
    }

    function it_deletes_the_current_model_from_datastore(\PDO $connection, \PDOStatement  $stmt, \PDOStatement $stmt2)
    {
        $connection->prepare('SELECT * FROM test WHERE `testid` = :testid')->willReturn($stmt);
        $stmt->execute(['testid' => 1])->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn([
            0 => [
                'testid'    => 1,
                'testvalue' => 'Test Value'
            ]
        ]);

        $connection->prepare('DELETE FROM test WHERE `testid` = :testid')->willReturn($stmt2);
        $connection->lastInsertId()->willReturn(0);
        $stmt2->execute(['testid' => 1])->willReturn(true);
        $stmt2->fetchAll(\PDO::FETCH_ASSOC)->willReturn();

        $this->find(1)->delete()->shouldReturn(true);
    }

    function it_throws_exception_if_deletion_set_not_defined()
    {
        $this->shouldThrow('\Exception')->duringDelete();
    }

    function it_throws_exception_if_deleting_multiple_rows(\PDO $connection, \PDOStatement $stmt)
    {
        $connection->prepare("SELECT * FROM test")
            ->willReturn($stmt);
        $stmt->execute()->willReturn(true);
        $stmt->fetchAll(\PDO::FETCH_ASSOC)->willReturn(
            [
                [
                    'testid'    => 1,
                    'testvalue' => 'Test 1'
                ],
                [
                    'testid'    => 2,
                    'testvalue' => 'Test 2'
                ]
            ]
        );
        $this->all()->get();
        $this->shouldThrow('\Exception')->duringDelete();
    }
}

class TestPDORepository extends Devtools\PDORepository
{
    protected $table = 'test';
    protected $primaryKey = 'testid';
    protected $required = ['testvalue'];
}
