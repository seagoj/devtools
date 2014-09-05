<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LogSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\Log');
    }

    function it_logs_tap_to_stout()
    {
        $this->write('Test', true)
            ->shouldReturn(true);

        $this->write('Test')
            ->shouldReturn(true);
    }

    function it_logs_tap_to_file()
    {
        $this->beConstructedWith(['type' => 'file']);
        $this
            ->write('Test', true)
            ->shouldReturn(true);

        $this->write('Test')
            ->shouldReturn(true);
    }

    function it_logs_html_to_stdout()
    {
        $this->beConstructedWith(['format' => 'html']);
        $this
            ->write('Test', true)
            ->shouldReturn(true);

        $this->write('Test')
            ->shouldReturn(true);
    }

    function it_logs_html_to_file()
    {
        $this->beConstructedWith([
            'type' => 'file',
            'format' => 'html'
        ]);
        $this
            ->write('Test', true)
            ->shouldReturn(true);

        $this->write('Test')
            ->shouldReturn(true);
    }

    function it_throws_exception_on_invalid_type()
    {
        $this->beConstructedWith(['type' => 'invalid']);
        $this->shouldThrow('\InvalidArgumentException')->duringWrite(['test', true]);
    }

    function it_throws_exception_on_invalid_format()
    {
        $this->beConstructedWith(['format' => 'invalid']);
        $this->shouldThrow('\InvalidArgumentException')->duringWrite(['Test', true]);
    }

    function it_creates_errorLog()
    {
        $this::errorLog()->shouldHaveType('\Devtools\Log');
    }

    function it_creates_debugLog()
    {
        $this::debugLog()->shouldHaveType('\Devtools\Log');
    }
}
