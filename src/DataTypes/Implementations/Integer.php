<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;

class Integer extends DataType
{
    public static $slug = 'integer';

    public function validate($data, array $options = []): bool
    {
        return is_int($data);
    }

    public function prepareForValidation($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d+$/', $data))
        ) {
            return (int) $data;
        }

        throw new \InvalidArgumentException('Unable to convert provided type of data to integer.');
    }
}
