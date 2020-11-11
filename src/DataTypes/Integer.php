<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\HasBeforeValidationHook;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Integer implements Validatable, HasBeforeValidationHook
{
    use HasDefaultValidationErrorMessage;

    public static $slug = 'integer';

    public function validate($data, array $options = []): bool
    {
        return is_int($data);
    }

    public function beforeValidation($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d+$/', $data))
        ) {
            return (int) $data;
        }

        throw new \InvalidArgumentException('Unable to apply before validation action on the provided type of data.');
    }
}
