<?php namespace Devtools;

require_once 'vendor/ircmaxell/password-compat/lib/password.php';

class Auth
{
    public static function hash($pass, $options = array())
    {
        $options = array_merge(
            array('cost' => 10),
            $options
        );
        return \password_hash($pass, PASSWORD_DEFAULT, $options);
    }

    public static function check($pass, $hash)
    {
        return \password_verify($pass, $hash);
    }
}
