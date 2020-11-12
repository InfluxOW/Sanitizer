<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\DataTypes\Contracts\PreparesForTransmission;
use Influx\Sanitizer\DataTypes\Contracts\PreparesForValidation;
use Influx\Sanitizer\DataTypes\Contracts\Validatable;

abstract class DataType implements Validatable, PreparesForTransmission, PreparesForValidation
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
