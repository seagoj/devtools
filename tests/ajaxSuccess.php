<?php
$resp = new \Devtools\Response;
$resp->data($_REQUEST);
$resp->message('This is a successful json call with which to test.');
echo $resp->json();
