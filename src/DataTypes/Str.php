<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class Str implements Validatable, Normalizable
{
    use HasDefaultNormalizationErrorMessage;
    use HasDefaultValidationErrorMessage;

    public static $slug = 'string';

    public function validate($data, array $options = []): bool
    {
        return is_string($data);
    }

    public function normalize($data, array $options = [])
    {
        try {
            return (string) $data;
        } catch (\Exception | \Error $e) {
            throw new NormalizationException($this->getNormalizationErrorMessage());
        }
    }
}
