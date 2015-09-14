<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class ValueObjectSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Devtools\MockValueObject');
        $this->beConstructedWith(
            ['param1' => 1, 'param2' => 2, 'param3' => 3]
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\ValueObject');
    }

    function it_retrieves_the_values()
    {
        $this->param1->shouldReturn(1);
        $this->param2->shouldReturn(2);
        $this->param3->shouldReturn(3);
    }
}

class MockValueObject extends Devtools\ValueObject
{
    protected $required = ['param1', 'param2'];
    protected $allowed  = ['param3'];
}
