<?php namespace Devtools;

use ReflectionClass;

abstract class IoC
{
    protected static $objectDefinitions = array();
    protected static $registryOfCreatedObjects = array();

    public static function bind($objectName, \Closure $callable)
    {
        self::$objectDefinitions[$objectName] = $callable;

        return true;
    }

    /* public static function make($objectName, $singleton = true) */
    /* { */
    /*     if ($singleton && isset(self::$registryOfCreatedObjects[$objectName])) { */
    /*         return self::$registryOfCreatedObjects[$objectName]; */
    /*     } */
    /*     if (!array_search($objectName, self::$objectDefinitions)) { */
    /*         $objectName::register(); */
    /*     } */
    /*     $func = self::$objectDefinitions[$objectName]; */
    /*     return self::$registryOfCreatedObjects[$objectName] = $func(); */
    /* } */

    public static function make($namespace, $singleton = true)
    {
        var_dump($namespace);
        if ($singleton && self::isInstantiated($namespace)) {
            return self::$registryOfCreatedObjects[$namespace];
        }

        if (self::isRegistered($namespace)) {
            return self::makeWithBinding($namespace);
        }

        if (self::objectRegistersBinding($namespace)) {
            $namespace::register();
            return self::make($namespace, $singleton);
        }

        return self::makeWithReflection($namespace);
    }

    private static function isInstantiated($namespace)
    {
        return isset(self::$registryOfCreatedObjects[$namespace]);
    }

    private static function isRegistered($namespace)
    {
        return isset(self::$objectDefinitions[$namespace]);
    }

    private static function makeWithBinding($namespace)
    {
        $func = self::$objectDefinitions[$namespace];
        return self::$registryOfCreatedObjects[$namespace] = $func();
    }

    private static function objectRegistersBinding($namespace)
    {
        return method_exists($namespace, 'register');
    }

    private static function makeWithReflection($namespace)
    {
        $reflection = new ReflectionClass($namespace);
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
