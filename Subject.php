<?php namespace Devtools;

interface Subject
{
    public function attach($observer);
    public function detach($observer);
    public function fire($event, $state = null);
}
