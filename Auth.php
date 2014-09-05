<?php namespace Devtools;
/**
 * Authentication class
 *
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/

/**
 * Class Auth
 *
 * @category Seagoj
 * @package  Markdown
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 *
 * Authentication tools for basic security
 *
 * Provides login, sanitization, and validation interface for basic PHP authentication
 **/
class Auth
{
    /**
     * Auth::hash
     *
     * Password hasher for the Auth class
     *
     * Hashes passwords using password_hash with salt
     *
     * @param string $pass    Raw password to be hashed
     * @param array  $options Password hashing options
     *
     * @return string Hashed password
     **/
    public static function hash($pass, $options = array())
    {
        $options = array_merge(
            array('cost' => 10),
            $options
        );
        return password_hash($pass, PASSWORD_DEFAULT, $options);
    }

    /**
     * check
     *
     * Checks string against hash
     *
     * @param string $pass Potential password
     * @param string $hash Hash of password
     *
     * @return Boolean Status  of comparison
     * @author Jeremy Seago <seagoj@gmail.com>
     **/
    public static function check($pass, $hash)
    {
        return password_verify($pass, $hash);
    }
}
