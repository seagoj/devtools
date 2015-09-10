<?php namespace spec\Devtools\Currency;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools\Currency\Dollar;

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

    function it_should_add_two_currencies()
    {
        $c2 = new Dollar(15);
        $this::add($this, $c2)->shouldHaveType('Devtools\Currency\Dollar');
        $this::add($this, $c2)->value()->shouldReturn(27.00);
    }

    function it_should_subtract_two_currencies()
    {
        $c2 = new Dollar(10);
        $this::subtract($this, $c2)->shouldHaveType('Devtools\Currency\Dollar');
        $this::subtract($this, $c2)->value()->shouldReturn(2.00);

        $this::subtract($c2, $this)->shouldHaveType('Devtools\Currency\Dollar');
        $this::subtract($c2, $this)->value()->shouldReturn(-2.00);
    }

    function it_should_multiply_a_currency()
    {
        $this::multiply($this, 3)->shouldHaveType('Devtools\Currency\Dollar');
        $this::multiply($this, 3)->value()->shouldReturn(36.00);
    }

    function it_should_divide_a_currency()
    {
        $this::divide($this, 2)->shouldHaveType('Devtools\Currency\Dollar');
        $this::divide($this, 2)->value()->shouldReturn(6.00);
    }
}
