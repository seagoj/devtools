<?php
/**
 * Debug Class
 *
 * Provides debugging options for fast development
 *
 * PHP version 5.3
 *
 * @category Seago
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version  GIT:
 * @link     http://seagoj.com
 **/

class Debug
{
    public static function setNoCache()
    {
        print "<META HTTP-EQUIV='CACHE-CONTROL' CONTENT='NO-CACHE'>\n<META HTTP-EQUIV='PRAGMA' CONTENT='NO-CACHE'>";
    }
}
