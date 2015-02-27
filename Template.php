<?php namespace Devtools;

class Template
{
    public function __construct($options = [])
    {
        $defaults = [];

        $this->config = array_merge($defaults, $options);
    }

    public static function autofill($template, $vars)
    {
        $template = is_file($template) ? file_get_contents($template) : $template;

        foreach ($vars as $var => $value) {
            $value = is_array($value) ? implode(', ', $value) : $value;
            if (($swap = str_replace('{{'.$var.'}}', $value, $template))!==false) {
                $template = $swap;
            }
        }

        return $template;
    }
}
