<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class LogExampleSpec extends ObjectBehavior
{
    function let(Devtools\FileLogger $log)
    {
        $this->beConstructedWith($log);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\LogExample');
    }

    function it_logs_on_event_fire($log)
    {
        $this->test();
        $log->handle('log', ['param1' => 'value1', 'param2' => 'value2'])->shouldBeCalled();
        /* $log->log(['param1' => 'value2', 'param2' =>'value2'])->shouldBeCalled(); */
    }
}
