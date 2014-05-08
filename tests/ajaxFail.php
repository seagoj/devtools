<?php
require_once 'autoloader.php';
$resp = new \Devtools\Response;
$resp->data($_REQUEST);
$resp->message('This is a failed json call for testing.');
echo $resp->json();
