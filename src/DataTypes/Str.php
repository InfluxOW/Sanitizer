<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class Str implements DataType, Normalizable
{
    public function validate($data, array $options = []): bool
    {
        return is_string($data);
    }

    public function normalize($data, array $options = [])
    {
        try {
            return (string) $data;
        } catch (\Exception $e) {
            throw new NormalizationException($this->getErrorMessage());
        }
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a string and couldn't be converted to it.";
    }
}