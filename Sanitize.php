<?php namespace Devtools;

class Sanitize
{

    public function __construct()
    {}

    /**
     * @param string $dirty
     */
    public static function str($dirty) {
        return strip_tags(
            htmlentities(
                stripslashes($dirty)
            )
        );
    }

    /* Unecessary in PDO */
    /* public static function sql($dirty) { */
    /*     return Sanitize::str( */
    /*         mysql_real_escape_string($dirty) */
    /*     ); */
    /* } */
}
