<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Helpers;

class OneTypeElementsArray implements DataType, Normalizable
{
    public function validate($data, array $options = []): bool
    {
        $correctTypeData = array_filter($data, function ($value) use ($options) {
            return Helpers::resolveDataTypeInstance($options['type'], $value)->validate();
        });

        return count($correctTypeData) === count($data);
    }

    public function normalize($data, array $options = [])
    {
        return array_map(function ($value) use ($options) {
            try {
                return Helpers::resolveDataTypeInstance($options['type'])->normalize();
            } catch (NormalizationException $e) {
                throw new NormalizationException($this->getErrorMessage());
            }
        }, $data);
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a one type elements array and couldn't be converted to it.";
    }
}