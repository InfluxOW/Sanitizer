<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\HasBeforeValidationHook;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Str implements Validatable, HasBeforeValidationHook
{
    use HasDefaultValidationErrorMessage;

    public static $slug = 'string';

    public function validate($data, array $options = []): bool
    {
        return is_string($data);
    }

    public function beforeValidation($data, array $options = [])
    {
        try {
            return (string) $data;
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException('Unable to handle provided data.');
        }
    }
}
