<?php namespace Devtools\Observer;

abstract class BaseObserver implements \SplObserver
{
    abstract public function update(\SplSubject $subject);
}
