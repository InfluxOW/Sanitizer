<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\HasBeforeValidationHook;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Double implements Validatable, HasBeforeValidationHook
{
    use HasDefaultValidationErrorMessage;

    public static $slug = 'float';

    public function validate($data, array $options = []): bool
    {
        return is_float($data);
    }

    public function beforeValidation($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d*\.?\d+$/', $data))
        ) {
            return (float) $data;
        }

        throw new \InvalidArgumentException('Unable to apply before validation action on the provided data.');
    }
}
