<?php namespace Devtools;

abstract class BaseSubject extends Collection implements Subject
{
    private $observers;

    public function attach($observable)
    {
        if (is_array($observable)) {
            $this->processEach($observable, null, 'attach', 'Devtools\Observer');
            return;
        }
        $this->observers[] = $observable;
    }

    public function detach($observable)
    {
        if (is_array($observable)) {
            $this->processEach($observable, null, 'detach', 'Devtools\Observer');
            return;
        }
        $this->removeObserver($observable);
    }

    public function fire($event, $state = null)
    {
        if (is_array($event)) {
            $this->processEach($event, $state, 'fire');
            return;
        }

        foreach ($this->observers as $observer) {
            $observer->handle($event, $state);
        }
    }

    public function observers()
    {
        return $this->observers;
    }

    private function removeObserver($observer)
    {
        $indexOfObserver = array_search($observer, $this->observers);
        if ($indexOfObserver !== false) {
            unset($this->observers[$indexOfObserver]);
        }
    }
}
