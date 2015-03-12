<?php namespace Devtools;

use Exception;

class Router
{
    protected static $resources = array();

    public static function resource($path, RestInterface $controller = null)
    {
        if (is_array($path)) {
            foreach ($path as $subpath => $subcontroller) {
                self::resource($subpath, $subcontroller);
            }
        }

        if (!is_null($controller)) {
            self::$resources[$path] = $controller;
        }
    }

    public static function call($uri)
    {
        if ($request = self::parseRequest($uri)) {
            $method = strtolower($_SERVER['REQUEST_METHOD']);
            return self::$resources[$request['route']]->$method($request['params']);
        }

        throw new Exception("Route for {$uri} not found.");
    }

    private static function isOptional($element)
    {
        return substr($element, 0, 1) === '('
            && substr($element, -1) === ')';
    }

    private static function parseOptional($element)
    {
        return substr($element, 1, strlen($element)-2);
    }

    private static function isParameter($element)
    {
        return substr($element, 0, 1) === ':';
    }

    private static function validateRouteWithOptional($pattern, $path)
    {
        $patternArray = explode('/', $pattern);
        $path    = explode('/', $path);
        $checkIndex = 0;
        $params  = array();

        foreach ($patternArray as $element) {
            if ($optional = self::isOptional($element)) {
                $element = self::parseOptional($element);
            }

            if (!$optional
                && $element !== $path[$checkIndex]
                && !self::isParameter($element)
            ) {
                return false;
            }

            if (count($path)-1 >= $checkIndex) {
                if ($element === $path[$checkIndex]) {
                    ++$checkIndex;
                    continue;
                }


                if (!empty($element) && self::isParameter($element)) {
                    $params[substr($element, 1)] = self::formatValue($path[$checkIndex]);
                    ++$checkIndex;
                    continue;
                }
            }

            if ($optional) {
                continue;
            }

            return false;
        }

        return array(
            'route'  => $pattern,
            'params' => $params
        );
    }

    public static function formatValue($value)
    {
        return is_numeric($value)
            ? intval($value)
            : $value;
    }

    public static function parseRequest($path)
    {
        foreach (array_keys(self::$resources) as $potentialRoute) {
            if ($route = self::validateRouteWithOptional($potentialRoute, $path)) {
                return $route;
            }
        }
        return false;
    }
}
