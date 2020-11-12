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

    public function prepareForTransmission($data, array $options = [])
    {
        return $data;
    }

    public function prepareForValidation($data, array $options = [])
    {
        return $data;
    }
}