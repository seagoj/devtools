<?php namespace spec\Devtools;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools;

class BaseSubjectSpec extends ObjectBehavior
{
    function let(
        Devtools\Observer $observer,
        Devtools\Observer $observer2,
        Devtools\Observer $observer3
    ) {
        $observer; $observer2; $observer3;
        $this->beAnInstanceOf('spec\Devtools\BaseSubjectMock');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Devtools\BaseSubject');
    }

    function it_attaches_observers($observer)
    {
        $this->attach($observer);
        $this->observers()->shouldReturn([$observer]);
    }

    function it_attaches_an_array_of_servers($observer, $observer2)
    {
        $this->attach([$observer, $observer2]);
        $this->observers()->shouldReturn([$observer, $observer2]);
    }

    function it_detaches_a_single_observer($observer, $observer2)
    {
        $this->attach($observer);
        $this->attach($observer2);
        $this->observers()->shouldReturn([$observer, $observer2]);
        $this->detach($observer);
        $this->observers()->shouldReturn([1 => $observer2]);
    }

    function it_detaches_an_array_of_observers(
        $observer, $observer2, $observer3
    ) {
        $this->attach(
            [
                $observer,
                $observer2,
                $observer3
            ]
        );
        $this->observers()->shouldReturn([$observer, $observer2, $observer3]);
        $this->detach([$observer, $observer3]);
        $this->observers()->shouldReturn([1 => $observer2]);
    }

    function it_fires_events_on_all_observers(
        $observer, $observer2, $observer3
    ) {
        $this->attach([$observer, $observer2, $observer3]);
        $observer->handle('Event1', null)->shouldBeCalled();
        $observer2->handle('Event1', null)->shouldBeCalled();
        $observer3->handle('Event1', null)->shouldBeCalled();
        $this->fire('Event1');
    }

    function it_fires_an_array_of_events(
        $observer, $observer2, $observer3
    ) {
        $this->attach([$observer, $observer2, $observer3]);
        $observer->handle('Event1', null)->shouldBeCalled();
        $observer2->handle('Event1', null)->shouldBeCalled();
        $observer3->handle('Event1', null)->shouldBeCalled();
        $observer->handle('Event2', null)->shouldBeCalled();
        $observer2->handle('Event2', null)->shouldBeCalled();
        $observer3->handle('Event2', null)->shouldBeCalled();
        $this->fire(['Event1', 'Event2']);
    }
}

class BaseSubjectMock extends Devtools\BaseSubject
{
}
