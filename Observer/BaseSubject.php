<?php namespace Devtools\Observer;

class BaseSubject implements \SplSubject
{
    private $observers;
    private $statuses;

    public function attach(\SplObserver $observer)
    {
        $this->observers[] = $observer;
        $this->statuses = new StatusCollection;
        return $this;
    }

    public function detach(\SplObserver $observer)
    {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            array_splice($this->observers, $key, 1);
        }
        return $this;
    }

    public function notify()
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
        $this->statuses->remove();
    }

    public function observers()
    {
        return $this->observers;
    }

    public function getStatus()
    {
        return $this->statuses->current();
    }
}

class StatusCollection
{
    private $collection;

    public function __construct()
    {
        $this->collection = array();
    }

    public function add($status)
    {
        $this->collection[] = $status;
    }

    public function current()
    {
        $this->collection[0];
    }

    public function remove()
    {
        array_shift($this->collection);
    }
}
