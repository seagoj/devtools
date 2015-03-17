<?php namespace Devtools;

class PDOAdapter
{
    public static function getConnectionString($parameters)
    {
        var_dump($parameters);
        return sprintf(
            self::getConnectionStringByType($parameters['type']),
            $parameters['type'],
            $parameters['host'],
            $parameters['db']
        );
    }

    public static function connect($parameters)
    {
        self::validateParameters($parameters);

        return new \PDO(
            self::getConnectionString($parameters),
            $parameters['username'],
            $parameters['password']
        );
    }

    private static function validateParameters($parameters)
    {
        $keys = array_keys($parameters);
        if (!in_array('type',  $keys)
            || !in_array('host', $keys)
            || !in_array('db', $keys)
            || !in_array('username', $keys)
            || !in_array('password', $keys)
        ) {
            throw new \Exception('Invalid connection options.');
        }
    }

    private static function getConnectionStringByType($type)
    {
        switch($type) {
        case 'mysql':
            return '%s:host=%s;dbname=%s';
            break;
        case 'firebird':
            return '%s:dbname=%s:%s';
            break;
        case 'dblib':
            return '%s:host=%s;%s';
            break;
        }
    }
}
