<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class StdOutLoggerSpec extends ObjectBehavior
{
    function let(Devtools\Formatter $formatter)
    {
        ob_start();
        $this->beConstructedWith($formatter);
    }

    function letgo()
    {
        ob_end_clean();
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\StdOutLogger');
    }

    function it_writes_to_stdout($formatter)
    {
        $this->write('Entry');
        $this->write('Entry2', true);
        $this->write('Entry3', false);

        $formatter->format('Entry', null)->shouldBeCalled();
        $formatter->format('Entry2', true)->shouldBeCalled();
        $formatter->format('Entry3', false)->shouldBeCalled();
        $formatter->footer()->willReturn('');
    }
}
