<?php namespace Devtools;

/**
 * Templating class
 *
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/
class Template
{
    /**
     * Configuration array for the class
     **/
    private $config;

    /**
     * Template::__construct
     *
     * Sets configuration for class
     *
     * @param array $options Array of configuration options
     *
     * @return void
     **/
    public function __construct($options = [])
    {
        $defaults = [];

        $this->config = array_merge($defaults, $options);
    }

    /**
     * Template::autofill
     *
     * Autofills the template variables
     *
     * @param string $template Template to fill
     * @param array  $vars     Array of variables and values to fill from
     *
     * @return   string  Filled template
     **/
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
