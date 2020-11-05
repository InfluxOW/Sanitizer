<?php

namespace Influx\Sanitizer\DataTypes;

class Structure implements DataType
{

    public function validate(): bool
    {
        // TODO: Implement validate() method.
    }

    public function normalize(): DataType
    {
        // TODO: Implement normalize() method.
    }

    public function getErrorMessage(): string
    {
        return "Provided value '{$this->value}' is not a string and couldn't be converted to a string.";
    }
}