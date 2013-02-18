<?php
require_once 'autoloader.php';

$auth = new \Devtools\Auth("user", "password");

if($auth->validate("user", "not password"))
	print "Valid";
else
	print "Not Valid";