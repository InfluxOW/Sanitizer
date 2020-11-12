<?php

namespace Influx\Sanitizer\Tests\Fixtures;

use Influx\Sanitizer\DataTypes\DataType;

class Divider extends DataType
{
    public static $slug = 'divider';

    public function validate($data, array $options = []): bool
    {
        return is_int($data) && $data % 2 === 0;
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

    public function afterValidation($data, array $options = [])
    {
        return $data / 2;
    }

}
