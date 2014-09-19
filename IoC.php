<?php namespace Devtools;

abstract class IoC
{
    protected static $objectDefinitions = array();
    protected static $registryOfCreatedObjects = array();

    public static function bind($objectName, \Closure $callable)
    {
        self::$objectDefinitions[$objectName] = $callable;

        return true;
    }

    public static function make($objectName)
    {
        if (isset(self::$registryOfCreatedObjects[$objectName])) {
            return self::$registryOfCreatedObjects[$objectName];
        }
        var_dump(self::$objectDefinitions);
        $func = self::$objectDefinitions[$objectName];
        return self::$registryOfCreatedObjects[$objectName] = $func();
    }
}
