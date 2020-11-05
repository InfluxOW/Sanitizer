<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Integer implements Validatable, Normalizable
{
    public function validate($data, array $options = []): bool
    {
        return is_int($data);
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data is not an integer ";
    }

    public function normalize($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d+$/', $data))
        ) {
            return (int) $data;
        }

        throw new NormalizationException($this->getNormalizationErrorMessage());
    }

    public function getNormalizationErrorMessage(): string
    {
        return "Unable to convert provided data to an integer.";
    }
}