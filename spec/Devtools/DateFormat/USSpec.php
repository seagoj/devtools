<?php namespace spec\Devtools\DateFormat;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools\DateFormat;

class USSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('10/15/1982');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\DateFormat\US');
    }

    function it_displays_date_in_proper_format()
    {
        $this->__toString()->shouldReturn('10/15/1982');
    }

    function it_creates_date_from_other_formats()
    {
        $this->from(
            new DateFormat\EU('1982/10/15')
        )->__toString()->shouldReturn('10/15/1982');
    }
}
