<?php namespace Devtools;

use Exception;

abstract class View
{
    protected $stylesheets;
    protected $scripts;
    protected $body;

    public function __get($property) {
        return $this->$property;
    }

    public static function stylesheet($path)
    {
        return "<link rel='stylesheet' href='{$path}'>\n";
    }

    public static function script($path)
    {
        return "<script src='{$path}'></script>\n";
    }
}
