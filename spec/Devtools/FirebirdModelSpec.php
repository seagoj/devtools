<?php
namespace spec\Devtools;

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
        )->shouldReturn('seagoj', 'Seago');
    }

    function it_sets_values_based_on_array()
    {
        $this->set(
            array(
                'first_name' => 'Jeremy'
            ),
            'users',
            array(
                'userid' => 1
            )
        )->shouldReturn(1000);
    }
}
