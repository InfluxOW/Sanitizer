<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Integer implements DataType, Normalizable
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate(): bool
    {
        return is_int($this->data);
    }

    public function normalize(): DataType
    {
        if (
            is_numeric($this->data) ||
            (is_string($this->data) && preg_match('/^-?\d+$/', $this->data))
        ) {
            return new self(
                (int) $this->data
            );
        }

        throw new NormalizationException($this->getErrorMessage());
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not an integer and couldn't be converted to it.";
    }

    public function getData()
    {
        return $this->data;
    }
}