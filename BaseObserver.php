<?php namespace Devtools;

use Exception;

abstract class BaseObserver implements Observer
{
    private $events = array();

    public function listen($event, $callback = null)
    {
        if (is_array($event) && is_null($callback)) {
            foreach ($event as $name => $callback) {
                $this->validateCallback($name, $callback);
            }
            $this->events = array_merge($this->events, $event);
        } else {
            $this->validateCallback($event, $callback);
            $this->events[$event] = $callback;
        }
    }

    public function handle($event)
    {
        if (array_search($event, $this->events)
            && method_exists($this, $event)
        ) {
            $this->$event();
        }
    }

    public function events()
    {
        return $this->events;
    }

    private function validateCallback($name, $callback)
    {
        if (!is_callable($callback, true)) {
            throw new Exception("{$name} is not callable.");
        }
    }
}
