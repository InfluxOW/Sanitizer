<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Str implements DataType
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function validate(): bool
    {
        return is_string($this->value);
    }

    public function normalize(): DataType
    {
        try {
            return new self(
                (string) $this->value
            );
        } catch (\Exception $e) {
            throw new NormalizationException($this->getErrorMessage());
        }
    }

    public function getErrorMessage(): string
    {
        return "Provided value is not a string and couldn't be converted to a string.";
    }

    public function getValue()
    {
        return $this->value;
    }
}