<?php namespace Devtools;

use Exception;

abstract class ValueObject
{
    protected $required = array();
    protected $allowed  = array();

    public function __construct($request)
    {
        foreach ($this->required as $parameter) {
            if (!isset($request[$parameter])) {
                throw new Exception("Invalid {__CLASS__} structure.");
            }
            $this->$parameter = $request[$parameter];
        }

        foreach ($this->allowed as $parameter) {
            if (isset($request[$parameter])) {
                $this->$parameter = $request[$parameter];
            }
        }
    }

    public function __get($parameter)
    {
        return $this->$parameter;
    }
}
