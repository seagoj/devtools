<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class BaseObserverSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Devtools\BaseObserverMock');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\BaseObserver');
    }

    function it_binds_event_listeners()
    {
        $this->listen('Event1');
        $this->events()->shouldBe(['Event1']);
    }

    function it_binds_event_listeners_from_array()
    {
        $this->listen(
            [
                'Event1',
                'Event2'
            ]
        );

        $this->events()->shouldBe(
            [
                'Event1',
                'Event2'
            ]
        );
    }

    function it_calls_bound_event_listeners()
    {
        $this->listen('Event1');

        $this->handle('Event1');
        $this->Event1(null)->shouldReturn(7);

        $this->handle('Event1', 7);
        $this->Event1(7)->shouldReturn(true);
    }
}

class BaseObserverMock extends Devtools\BaseObserver
{
    public function Event1($state)
    {
        switch($state) {
        case 7:
            return true;
        default:
            return 7;
        }
    }
}
