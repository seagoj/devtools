<?php namespace spec\Devtools\Observer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Devtools\Observer;

class BaseSubjectSpec extends ObjectBehavior
{
    function let()
    {
        $this->beAnInstanceOf('spec\Devtools\Observer\MockSubject');
    }

    function it_adds_observer_to_collection(MockObserver $observer)
    {
        $this->attach($observer);
        $this->observers()->shouldReturn([$observer]);

        $this->attach($observer);
        $this->observers()->shouldReturn([$observer, $observer]);

        $this->attach($observer)->attach($observer);
        $this->observers()->shouldReturn([$observer, $observer, $observer, $observer]);
    }

    function it_removes_observer_from_collection(MockObserver $observer)
    {
        $this->attach($observer)->attach($observer)->attach($observer);
        $this->observers()->shouldReturn([$observer, $observer, $observer]);

        $this->detach($observer);
        $this->observers()->shouldReturn([$observer, $observer]);

        $this->detach($observer)->detach($observer);
        $this->observers()->shouldReturn([]);

        $this->detach($observer);
        $this->observers()->shouldReturn([]);
    }

    function it_notifes_observers(MockObserver $observer)
    {
        $this->attach($observer);
        $this->notify();
        $observer->update($this)->shouldBeCalled();
    }
}

class MockObserver extends Observer\BaseObserver
{
    public function update(\SplSubject $subject)
    {
        $subject->getStatus();
    }
}

class MockSubject extends Observer\BaseSubject
{
}
