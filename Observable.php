<?php namespace Devtools;

interface Observable
{
    public function attach($observer);
    public function detach($observer);
    public function fire($event);
}
