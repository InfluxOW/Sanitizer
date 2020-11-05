<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Double implements DataType, Normalizable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate(): bool
    {
        return is_float($this->data);
    }

    public function normalize(): DataType
    {
        if (
            is_numeric($this->data) ||
            (is_string($this->data) && preg_match('/^-?\d*\.?\d+$/', $this->data))
        ) {
            return new self(
                (float) $this->data
            );
        }

        throw new NormalizationException($this->getErrorMessage());
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a float and couldn't be converted to it.";
    }
}