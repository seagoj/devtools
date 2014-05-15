<?php

class Sanitize
{
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

    public static function sql($dirty) {
        return Sanitize::str(
            mysql_real_escape_string($dirty)
        );
    }
}
