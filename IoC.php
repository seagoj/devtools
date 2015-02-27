<?php namespace Devtools;

use ReflectionClass;
use Closure;

abstract class IoC
{
    protected static $objectDefinitions = array();
    protected static $registryOfCreatedObjects = array();

    public static function bind($objectName, Closure $callable)
    {
        self::$objectDefinitions[$objectName] = $callable;

        return true;
    }

    public static function make($objectName, $singleton = true)
    {
        if ($singleton && self::isInstantiated($objectName)) {
            return self::$registryOfCreatedObjects[$objectName];
        }

        if (self::isRegistered($objectName)) {
            return self::makeWithBinding($objectName);
        }

        if (self::objectRegistersBinding($objectName)) {
            $objectName::register();
            return self::make($objectName, $singleton);
        }

        return self::makeWithReflection($objectName);
    }

    private static function isInstantiated($objectName)
    {
        return isset(self::$registryOfCreatedObjects[$objectName]);
    }

    private static function isRegistered($objectName)
    {
        return isset(self::$objectDefinitions[$objectName]);
    }

    private static function makeWithBinding($objectName)
    {
        $func = self::$objectDefinitions[$objectName];
        return self::$registryOfCreatedObjects[$objectName] = $func();
    }

    private static function objectRegistersBinding($objectName)
    {
        return method_exists($objectName, 'register');
    }

    private static function makeWithReflection($objectName)
    {
        if (!class_exists($objectName)) {
            return false;
        }

        $reflection = new ReflectionClass($objectName);
        $dependencies = $reflection->getMethod('__construct')->getParameters();
        $params = array();
        foreach ($dependencies as $dependency) {
            array_push(
                $params,
                self::make(
                    $dependency->getClass()->getName()
                )
            );
        }
        return $reflection->newInstanceArgs($params);
    }
}
