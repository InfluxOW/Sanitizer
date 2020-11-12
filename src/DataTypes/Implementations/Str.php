<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;

class Str extends DataType
{
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
            throw new \InvalidArgumentException('Unable to convert provided type of data to string.');
        }
    }
}
