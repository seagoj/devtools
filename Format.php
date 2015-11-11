<?php namespace Devtools;

use Devtools\DateFormat;

class Format
{
    public static function snakeToCamelCase($underscore)
    {
        return preg_replace_callback(
            "/(_\\w)/u",
            function ($match) {
                return strToUpper(substr($match[0], 1));
            },
            $underscore
        );
    }

    public static function camelToSnakeCase($camelCase)
    {
        return preg_replace_callback(
            "/([A-Z])/u",
            function ($match) {
                return '_'.strtolower($match[0]);
            },
            $camelCase
        );
    }

    public static function sql($dirty)
    {
        return self::stripWhitespace($dirty);
    }

    public static function stripWhitespace($dirty)
    {
        return preg_replace("/[ \\t\\n]+/u", " ", $dirty);
    }

    public static function stringify($array, $force = false, $quotation="'")
    {
        $ret = "";
        if (!is_array($array)) {
            $array = array($array);
        }
        foreach ($array as $element) {
            if (!empty($ret)) {
                $ret .= ",";
            }
            $ret .= (!$force && is_numeric($element))
                ? $element
                : $quotation.$element.$quotation;
        }
        return $ret;
    }

    public static function mysqlDate($date)
    {
        $mysql = new DateFormat\MySQL();
        return $mysql->from(new DateFormat\US($date))->__toString();
    }
}
