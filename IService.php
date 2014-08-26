<?php

namespace Devtools;

interface IService
{
    public function data(array $data);
    public static function getRequest();
    public function php();
    public function json();
    public function delimited();
    public function serialize();
}
