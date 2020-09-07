<?php

namespace App\Services\Lib;

trait ServiceTrait
{
    private $objectExtend = [];

    final public function extend() {
        foreach ($objects = func_get_args() as &$object) {
            if (is_object($object) && !$object instanceOf self) {
                $this->objectExtend[] = $object;
                
                if (is_callable([$object, 'syncExtend']))
                    $object->syncExtend($this, $objects);
            }
        }
    }

    final public function syncExtend(&$object = null, array $objects) {
        if (is_object($object) && !$object instanceOf self && !in_array($object, $this->objectExtend))
            $this->objectExtend[] = $object;

        foreach ($objects as &$object)
            if (is_object($object) && !$object instanceOf self && !in_array($object, $this->objectExtend))
                $this->objectExtend[] = $object;

        return $this;
    }

    final public function __call($method, $args) {
        if (method_exists($this, $method))
            return $this->{$method}(... $args);
        else {
            foreach ($this->objectExtend as $i => &$object) {
                if (method_exists($object, $method))
                    return $object->{$method}(... $args);
            }
        }

        throw new \Exception('Call to undefined method ' . get_called_class() .  '::' . $method . '()');
    }

    final public function __get($key) {
        if (array_key_exists($key, $this))
            return $this->{$key};

        foreach ($this->objectExtend as &$object) {
            if (array_key_exists($key, $object))
                return $object->{$key};
        }

        trigger_error('Undefined property: ' . get_called_class() . '::$' . $key, E_USER_NOTICE);
    }

    final public function __isset($key) {
        if (array_key_exists($key, $this))
            return true;

        foreach ($this->objectExtend as &$object) {
            if (array_key_exists($key, $object))
                return true;
        }

        return false;
    }
}