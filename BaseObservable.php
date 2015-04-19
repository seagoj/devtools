<?php namespace Devtools;

abstract class BaseObservable extends Collection implements Observable
{
    private $observers;

    public function attach($observer)
    {
        if (is_array($observer)) {
            $this->processEach($observer, 'attach', 'Devtools\Observer');
            return;
        }
        $this->observers[] = $observer;
    }

    public function detach($observer)
    {
        if (is_array($observer)) {
            $this->processEach($observer, 'detach', 'Devtools\Observer');
            return;
        }
        $this->removeObserver($observer);
    }

    public function fire($event)
    {
        if (is_array($event)) {
            $this->processEach($event, 'fire');
            return;
        }
        foreach ($this->observers as $observer) {
            $observer->handle($event);
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
