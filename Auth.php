<?php
/**
 * Authentication class
 *
 * @category Seagoj
 * @package  Devtools
 * @author   Jeremy Seago <seagoj@gmail.com>
 * @license  http://github.com/seagoj/Devtools/LICENSE MIT
 * @link     http://github.com/seagoj/Devtools
 **/

namespace Devtools;

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
     * Email address provded
     *
     * Used as username for authentication
     **/
    private $email;

    /**
     * Hash of password provided
     *
     * Password provided has been hashed using password_hash
     **/
    private $hash;

    /**
     * Auth::__construct
     *
     * Constructor for Auth class
     *
     * Initializes Auth.email and Auth.pass if provided
     *
     * @param string $email Email to be used in authentication; defaults to
     *                          null
     * @param string $pass Password to be used in authentication; defaults
     *                          to null
     *
     * @return void
     **/
    public function __construct($email = null, $pass = null)
    {
        if (!is_null($email) && !is_null($pass)) {
            $this->email = $email;
            $this->hash = $this->hash($pass);
        }
    }

    /**
     * Auth::validate
     *
     * Validation for the Auth class
     *
     * Validates accepted values for email and password against expected
     *
     * @param string $email_attempt Email provided in attempt to
     *                                  authenticate
     * @param string $pass_attempt Password provided in attempt to
     *                                  authenticate
     *
     * @return boolean Result of validation
     **/
    public function validate($email_attempt, $pass_attempt)
    {
        return 'PHP_VERSION_ID'>=5.5 ?
            ($this->email===$email_attempt) && password_verify($this->hash($pass_attempt), $this->hash) :
            ($this->email===$email_attempt) && ($this->hash===$pass_attempt);
    }

    /*
    public function sanitize($pass)
    {
        $db = new \PDO('sqlite::memory:');

        return $db->prepare($pass);
    }
    */

    /**
     * Auth::hash
     *
     * Password hasher for the Auth class
     *
     * Hashes passwords using password_hash with salt
     *
     * @param string $pass Raw password to be hashed
     *
     * @return string Hashed password
     **/
    public function hash($pass)
    {
        $salt = $this->email;
        while (strlen($salt)<22) {
            $salt.=$this->email;
        }

        $options = [
            'salt'=> $salt
        ];

        // @codeCoverageIgnoreStart
        if ('PHP_VERSION_ID'>=5.5) {
            return password_hash(Auth::sanitize($pass), PASSWORD_DEFAULT, $options);
        } else {
            return $pass;
        }
        // @codeCoverageIgnoreEnd
    }
}
