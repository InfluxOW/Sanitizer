<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\DataTypes\Contracts\DataType as DataTypeContract;

abstract class DataType implements DataTypeContract
{
    public function getValidationErrorMessage(): string
    {
        $readableDataTypeName = ucfirst(getReadableName(static::$slug ?? get_class($this)));

        return "{$readableDataTypeName} is not the type of provided data.";
    }

    public function afterValidation($data, array $options = [])
    {
        return $data;
    }

    public function beforeValidation($data, array $options = [])
    {
        return $data;
    }
}