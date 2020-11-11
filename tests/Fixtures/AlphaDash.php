<?php

namespace Influx\Sanitizer\Tests\Fixtures;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class AlphaDash implements Validatable, Normalizable
{
    use HasDefaultValidationErrorMessage;
    use HasDefaultNormalizationErrorMessage;

    public static $slug = 'alpha_dash';

    public function validate($data, array $options = []): bool
    {
        try {
            return preg_match('/^[a-zA-Z0-9-_]+$/', $data);
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException("Unable to handle non string value.");
        }
    }

    public function normalize($data, array $options = [])
    {
        try {
            return preg_replace('/[^a-zA-Z0-9-_]+/', '', $data);
        } catch (\Exception | \Error $e) {
            throw new NormalizationException($this->getNormalizationErrorMessage());
        }
    }
}
