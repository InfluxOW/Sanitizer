<?php

namespace Influx\Sanitizer\Tests\Fixtures;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Divider implements Validatable, Normalizable
{
    use HasDefaultValidationErrorMessage;
    use HasDefaultNormalizationErrorMessage;

    public static $slug = 'divider';

    public function validate($data, array $options = []): bool
    {
        return is_int($data);
    }

    public function normalize($data, array $options = [])
    {
        if (
            is_numeric($data) ||
            (is_string($data) && preg_match('/^-?\d+$/', $data))
        ) {
            return (int) $data / 2;
        }

        throw new NormalizationException($this->getNormalizationErrorMessage());
    }
}
