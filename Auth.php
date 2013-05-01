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
			$this->_hash = $pass;
		}
	}

	public function validate($email_attempt, $pass_attempt)
	{
		return $this->_email==$email_attempt &&
			password_verify($pass_attempt, $this->_hash);
	}

	public function sanitize($pass)
	{
		return(mysql_real_escape_string($pass));
	}

	public function hash($pass)
	{
		$options = [
			$salt => $this->_email
		];

		return password_hash(Auth::sanitize($pass), PASSWORD_DEFAULT, $options);
	}
}
