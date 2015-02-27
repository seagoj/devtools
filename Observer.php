<?php namespace Devtools;

interface Observer
{
    public function handle($event, $state = null);
}
