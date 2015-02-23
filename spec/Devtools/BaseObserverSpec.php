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
        $this->listen('Event1', array($this, 'callback'));
        $this->events()->shouldBe(['Event1' => array($this, 'callback')]);
    }

    function it_binds_event_listeners_from_array()
    {
        $this->listen(
            [
                'Event1' => array($this, 'callback'),
                'Event2' => array($this, 'callback')
            ]
        );

        $this->events()->shouldBe(
            [
                'Event1' => array($this, 'callback'),
                'Event2' => array($this, 'callback')
            ]
        );
    }

    function callback()
    {
    }
}

class BaseObserverMock extends Devtools\BaseObserver
{
}
