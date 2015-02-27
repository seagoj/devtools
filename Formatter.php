<?php namespace Devtools;

abstract class Formatter
{
    abstract public function format($content, $result);
    public function header()
    {
        return '';
    }

    public function footer()
    {
        return '';
    }

    public function stringify($content)
    {
        return (is_array($content) || is_object($content))
            ? serialize($content)
            : $content;
    }
}
