<?php namespace Devtools;

use Exception;

abstract class BaseObserver extends Collection implements Observer
{
    private $events = array();

    public function listen($event)
    {
        if (is_array($event)) {
            $this->processEach($event, null, 'listen');
            return;
        }

        array_push($this->events, $event);
    }

    public function handle($event, $state = null)
    {
        if (array_search($event, $this->events)
            && method_exists($this, $event)
        ) {
            return $this->$event($state);
        }
    }

    public function events()
    {
        return $this->events;
    }
}
