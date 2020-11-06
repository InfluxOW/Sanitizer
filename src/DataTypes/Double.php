<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Double implements Validatable, Normalizable
{
    use HasDefaultNormalizationErrorMessage;
    use HasDefaultValidationErrorMessage;

    public static $slug = 'float';

    public function validate($data, array $options = []): bool
    {
        return is_float($data);
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
}