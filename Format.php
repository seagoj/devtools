<?php namespace Devtools;

class Format
{
    public static function underscoreToCamelCase($underscore)
    {
        return preg_replace_callback(
            "/(_\\w)/u",
            function ($match) {
                return strToUpper(substr($match[0], 1));
            },
            $underscore
        );
    }

    public static function camelCaseToUnderscore($camelCase)
    {
        return preg_replace_callback(
            "/([A-Z])/u",
            function ($match) {
                return '_'.strtolower($match[0]);
            },
            $camelCase
        );
    }
}
