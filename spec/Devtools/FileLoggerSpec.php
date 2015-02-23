<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class FileLoggerSpec extends ObjectBehavior
{
    function let(Devtools\Formatter $formatter)
    {
        $this->beConstructedWith('test.log', $formatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\FileLogger');
    }

    function it_writes_to_file($formatter)
    {
        $this->write('Log1')->shouldNotReturn(false);
        $this->write('Log2', true)->shouldNotReturn(false);
        $this->write('Log3', false)->shouldNotReturn(false);

        $formatter->format('Log1', null)->shouldBeCalled();
        $formatter->format('Log2', true)->shouldBeCalled();
        $formatter->format('Log3', false)->shouldBeCalled();
        /* $formatter->footer()->shouldBeCalled(); */
    }
}
