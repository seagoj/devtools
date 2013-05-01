<?php

namespace Devtools;

class Auth
{
	private $_email;
	private $_hash;

	function __construct($email=null,$pass=null)
	{
		if($email!=null) {
			$this->_email = $email;
		}
		if($pass!=null) {
			$this->_hash = $this->hash($pass);
		}
	}

	public function validate($email_attempt, $pass_attempt)
	{
        if('PHP_VERSION_ID'>=5.5)
            return $this->_email==$email_attempt &&
		        password_verify($this->hash($pass_attempt), $this->_hash);
        else
            return true;
	}

	public function sanitize($pass)
	{
        $db = new \PDO('sqlite::memory:');
		return $db->prepare($pass);
	}

	public function hash($pass)
	{
        $salt = $this->_email;
        while(strlen($salt)<22) {
            $salt.=$this->_email;       
        }

		$options = [
			'salt'=> $salt
		];

        if('PHP_VERSION_ID'>=5.5) 
    		return password_hash(Auth::sanitize($pass), PASSWORD_DEFAULT, $options);
        else return $pass;
	}
}
