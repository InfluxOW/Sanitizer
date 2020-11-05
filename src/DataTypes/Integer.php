<?php

namespace Influx\Sanitizer\DataTypes;

class Integer implements DataType
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function validate(): bool
    {
        return is_int($this->value);
    }

    public function normalize(): DataType
    {
        if (is_numeric($this->value)) {
            return new self(
                (int) $this->value
            );
        }

//        if (is_string($this->value) && preg_match());

        throw new \NormalizationException($this->getErrorMessage());
    }

    public function getErrorMessage(): string
    {
        try {
            return "Provided value '{$this->value}' is not an integer and couldn't be converted to an integer.";
        } catch (\Exception $e) {
            return "Provided value is not an integer and couldn't be converted to an integer.";
        }
    }
}