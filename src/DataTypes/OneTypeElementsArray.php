<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\App;
use Influx\Sanitizer\Sanitizer;

class OneTypeElementsArray implements Validatable, Normalizable
{
    public function validate($data, array $options = []): bool
    {
        $dt = Sanitizer::resolveDataTypeInstance($options);

        $correctTypeData = array_filter($data, function ($value) use ($options) {
            return Sanitizer::resolveDataTypeInstance($options)->validate();
        });

        return count($correctTypeData) === count($data);
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data is not a one type elements array.";
    }

    public function normalize($data, array $options = [])
    {
        return array_map(function ($value) use ($options) {
            try {
                return App::resolveDataTypeInstance($options)->normalize();
            } catch (NormalizationException $e) {
                throw new NormalizationException($this->getNormalizationErrorMessage());
            }
        }, $data);
    }

    public function getNormalizationErrorMessage(): string
    {
        return "Unable to convert provided data to a one type elements array.";
    }
}