<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Double implements Validatable, Normalizable
{
    public static $slug = 'float';

    public function validate($data, array $options = []): bool
    {
        return is_float($data);
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data is not a {$this->slug}.";
    }

    public function normalize($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d*\.?\d+$/', $data))
        ) {
            return (float) $data;
        }

        throw new NormalizationException($this->getNormalizationErrorMessage());
    }

    public function getNormalizationErrorMessage(): string
    {
        return "Unable to convert provided data to a float.";
    }
}