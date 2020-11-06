<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Str implements Validatable, Normalizable
{
    public static $slug = 'string';

    public function validate($data, array $options = []): bool
    {
        return is_string($data);
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data is not a string.";
    }

    public function normalize($data, array $options = [])
    {
        try {
            return (string) $data;
        } catch (\Exception $e) {
            throw new NormalizationException($this->getNormalizationErrorMessage());
        }
    }

    public function getNormalizationErrorMessage(): string
    {
        return "Unable to convert provided data to a string.";
    }
}