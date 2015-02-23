<?php namespace Devtools;

use Exception;

abstract class Collection
{
    protected function processEach(
        $objects, $callback, $requiredInstance = null
    ) {
        foreach ($objects as $key => $object) {
            $this->validateObject($object, $requiredInstance, $key);
            if (!is_numeric($key)) {
                $object = array($key => $object);
            }
            call_user_func(array($this, $callback), $object);
        }
    }

    private function validateObject($object, $requiredInstance)
    {
        if (!is_null($requiredInstance)
            && !is_subclass_of($object, $requiredInstance)
        ) {
            throw new Exception(
                'Invalid observer: ' . get_class($object)
            );
        }
    }
}
