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
                    $result .= self::stylesheet($stylesheet);
                }
            } else {
                $result .= self::stylesheet($this->stylesheets);
            }
            return $result;
        case 'script':
            $result = '';
            if (is_array($this->scripts)) {
                foreach ($this->scripts as $scripts) {
                    $result .= self::script($script);
                }
            } else {
                $result .= self::script($this->scripts);
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
