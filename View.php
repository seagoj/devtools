<?php namespace Devtools;

use Exception;

abstract class View
{
    protected $stylesheets;
    protected $scripts;

    public function __get($property)
    {
        if (isset($this->$property)) {
            return $this->$property;
        }

        switch($property) {
        case 'stylesheet':
            $result = '';
            if (is_array($this->stylesheets)) {
                foreach ($this->stylesheets as $stylesheet) {
                    $result .= $this->stylesheet($stylesheet);
                }
            } else {
                $result .= $this->stylesheet($this->stylesheets);
            }
            return $result;
        case 'script':
            $result = '';
            if (is_array($this->scripts)) {
                foreach ($this->scripts as $scripts) {
                    $result .= $this->script($script);
                }
            } else {
                $result .= $this->script($this->scripts);
            }
            return $result;
        default:
            throw new Exception("Undefined property: {$property}");
        }
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
