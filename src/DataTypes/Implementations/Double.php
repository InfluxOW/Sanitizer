<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;

class Double extends DataType
{
    public static $slug = 'float';

    public function validate($data, array $options = []): bool
    {
        return is_float($data);
    }

    public function prepareForValidation($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d*\.?\d+$/', $data))
        ) {
            return (float) $data;
        }

        throw new \InvalidArgumentException('Unable to convert provided type of data to float.');
    }
}
