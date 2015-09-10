<?php namespace spec\Devtools\Currency;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class DollarSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(12);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Currency\Dollar');
    }

    function it_stores_formatted_value()
    {
        $this->value()->shouldReturn(12.00);
    }

    function it_prints_formatted_value_as_string()
    {
        $this->__toString()->shouldReturn('$12.00');
    }
}
