<?php namespace Devtools\Observer;

class BaseSubject implements \SplSubject
{
    private $observers;
    private $statuses;

    public function attach(\SplObserver $observer)
    {
        $this->observers[] = $observer;
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
    }

    public function observers()
    {
        return $this->observers;
    }

    public function getStatus()
    {
        return $this->statuses;
    }

    protected function status($status)
    {
        $this->statuses[] = $status;
    }
}
