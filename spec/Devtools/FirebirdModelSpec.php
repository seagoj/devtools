<?php
namespace spec\Devtools;

require_once 'InterbaseMock.php';

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FirebirdModelSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('TestResource');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\FirebirdModel');
    }

    function it_gets_a_single_value_as_string()
    {
        $this->get('user_name', 'users', array('user_id' => 1))
            ->shouldReturn('seagoj');;
    }

    function it_gets_multiple_values_as_array()
    {
        $this->get(
            array(
                'user_name',
                'last_name'
            ),
            'users',
            array('user_id' => 1)
        )->shouldReturn(
            array(
                'user_name' => 'seagoj',
                'last_name' => 'Seago'
            )
        );
    }

    function it_sets_values_based_on_array()
    {
        $this->set(
            array(
                'first_name' => 'Jeremy'
            ),
            'users',
            array(
                'user_id' => 1
            )
        )->shouldReturn(1000);
    }

    function it_returns_all_values_from_a_collection()
    {
        $this->getAll(
            'users'
        )->shouldReturn(
            array(
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
            )
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
