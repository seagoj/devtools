<?php namespace Devtools;

abstract class Formatter
{
    abstract public function header();
    abstract public function format($content, $result);
    abstract public function footer();

    private function stringify($content)
    {
        return (is_array($content) || is_object($content))
            ? serialize($content)
            : $content;
    }
}
