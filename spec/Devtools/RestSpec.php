<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools\PDORepository;
use Devtools\Log;
use Devtools\Format;

class RestSpec extends ObjectBehavior
{
    function let(
        PDORepository $repository,
        Log $log
    ) {
        $_SERVER['phpspec'] = true;
        $this->beConstructedWith($repository, $log);
    }

    function letgo()
    {
        $_REQUEST = [];
    }

    function mockValidGet()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        return $this;
    }

    function withId($repository)
    {
        $_SERVER['REQUEST_URI'] = '/api/test/1';

        switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $repository->query(
                Format::stripWhitespace(
                    "SELECT *
                    FROM test
                    WHERE test_id=:id"
                ),
                [ 'id' => '1' ],
                true
            )->willReturn(
                [
                    'test_id' => 1,
                    'name'   => 'Jim',
                    'role'   => 'sorter'
                ]
            );
            break;
        }

        return $this;
    }

    function withParameters(&$repository)
    {
        $_REQUEST['name'] = 'Jim';

        $_SERVER['REQUEST_URI'] = '/api/test';

        switch($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $repository->query(
                Format::stripWhitespace(
                    "SELECT *
                    FROM test
                    WHERE name=:name"
                ),
                [ 'name' => 'Jim' ],
                true
            )->willReturn(
                [
                    'test_id' => 1,
                    'name'   => 'Jim',
                    'role'   => 'sorter'
                ]
            );
            break;
        }

        return $this;
    }

    function it_is_initializable($repository)
    {
        $this->mockValidGet()->withId($repository);
        $this->shouldHaveType('Devtools\Rest');
    }

    function it_determines_root_of_uri($repository)
    {
        $this->mockValidGet()->withId($repository);

        $this::getRoot()->shouldBe('/api/');
    }

    function it_pulls_method_from_request($repository)
    {
        $this->mockValidGet()->withId($repository);

        $this->method->shouldBe('GET');
    }

    function it_pulls_request_from_uri($repository)
    {
        $this->mockValidGet()->withId($repository);

        $this->request->shouldBe(['test','1']);
    }

    function it_pulls_parameters_from_uri($repository)
    {
        $this->mockValidGet()->withParameters($repository);

        $this->parameters->shouldBe(['name' => "Jim"]);
    }

    function it_performs_a_database_call_based_on_the_request($repository)
    {
        $this->mockValidGet()->withId($repository);

        $this->process()->shouldReturn(
            [
                'test_id' => 1,
                'name'   => 'Jim',
                'role'   => 'sorter'
            ]
        );
    }

    function it_performs_a_database_call_using_parameters_as_where_clause($repository)
    {
        $this->mockValidGet()->withParameters($repository);

        $this->process()->shouldReturn(
            [
                'test_id' => 1,
                'name' => 'Jim',
                'role' => 'sorter'
            ]
        );
    }
}
