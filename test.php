<?php

$data = array(
    "connect"=> true,
	"type"=> "firebird",
   	"host"=> "TEST-BPSAPP1",
   	"location"=> "fl",
   	"environment"=> "previousday",
   	"dba"=> "SYSDBA",
   	"password"=> "masterkey"
);
//file_put_contents('firebird-model.json', json_encode($data));
$json = file_get_contents('firebird-model.json');
var_dump((array) json_decode($json));
