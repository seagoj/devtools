<?php namespace Devtools;

require_once 'vendor/ircmaxell/password-compat/lib/password.php';

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
        return \password_verify($pass, $hash);
    }
}
