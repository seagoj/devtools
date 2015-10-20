<?php namespace Devtools;

use Guzzle\Http\Client;

class Request
{
    public static $client;

    public function __construct($base_uri = null)
    {
        if (is_null($base_uri)) {
            $base_uri = "http://{$_SERVER['SERVER_NAME']}/";
        }

        self::$client = new Client($base_uri);
    }

    public static function get($uri, $data = null)
    {
        return self::$client->get(
            $uri,
            array(),
            array('query' => $data)
        )->send()->json();
    }

    public static function post($uri, $data = null)
    {
        return self::$client->post(
            $uri,
            array(),
            $data
        )->send()->json();
    }
}
