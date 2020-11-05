<?php

namespace Influx\Sanitizer;

class Helpers
{
    public static function resolveDataTypeInstance($rule)
    {
        if (array_key_exists('name', $rule)) {
            return new $rule();
        }

//        if (is_string($rule)) {
//            return new $rule($value);
//        }
//
//        if (is_array($rule) && count($rule) > 0 && is_string($rule[0])) {
//            return count($rule) === 1 ?
//                new $rule[0]($value) :
//                new $rule[0]($value, ...array_diff_key($rule, [0]));
//        }

        throw new \InvalidArgumentException("Unable to resolve specified data type instance.");
    }
}