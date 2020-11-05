<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;

class Structure implements Validatable
{
    public function validate($data, array $options = []): bool
    {
        foreach ($options['keys'] as $key => $value) {
            if (is_array($value) && array_key_exists($key, $data) && is_array($data[$key])) {
                if (static::validate($data[$key], $value)) {
                    continue;
                }

                return false;
            }

            if (array_key_exists($value, $data)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data doesn't match with the specified structure.";
    }
}