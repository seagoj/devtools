<?php namespace Devtools;

use Exception;

abstract class View
{
    protected $stylesheetCollection;
    protected $scriptCollection;
    protected $body;

    public function __get($property)
    {
        switch($property) {
        case 'stylesheet':
        case 'script':
            return $this->processCollection($property);
        default:
            return $this->$property;
        }
    }

    private function processCollection($property)
    {
        $collection = $property.'Collection';
        if (!is_array($this->$collection)) {
            return self::$property($this->$collection);
        }

        $result = '';
        foreach ($this->$collection as $item) {
            $result .= self::$property($item);
        }
        return $result;
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
