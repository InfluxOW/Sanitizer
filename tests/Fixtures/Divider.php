<?php

namespace Influx\Sanitizer\Tests\Fixtures;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Divider implements Validatable, Normalizable
{
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
            if ($data % 2 === 0) {
                return $data / 2;
            }
        }

        throw new NormalizationException($this->getNormalizationErrorMessage());
    }

    public function getNormalizationErrorMessage(): string
    {
        return 'Unable to normalize value to an even number.';
    }

    public function getValidationErrorMessage(): string
    {
        return 'Value is not an even number.';
    }
}
