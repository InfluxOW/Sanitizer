<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Str implements DataType, Normalizable
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate(): bool
    {
        return is_string($this->data);
    }

    public function normalize(): DataType
    {
        try {
            return new self(
                (string) $this->data
            );
        } catch (\Exception $e) {
            throw new NormalizationException($this->getErrorMessage());
        }
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a string and couldn't be converted to it.";
    }

    public function getData()
    {
        return $this->data;
    }
}