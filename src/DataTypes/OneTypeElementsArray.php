<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\App;
use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Services\Resolver;
use Influx\Sanitizer\Traits\NeedsAvailableDataTypesList;

class OneTypeElementsArray implements Validatable, Normalizable
{
    use NeedsAvailableDataTypesList;

    public function validate($data, array $options = []): bool
    {
        $this->checkOptions($options);

        $dt = (new Resolver($options['available_data_types']))->getDataTypeInstance($options['elements_type']);

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
        $this->checkOptions($options);

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

    private function checkOptions(array $options): void
    {
        if (! array_key_exists('elements_type', $options)) {
            throw new \InvalidArgumentException("Please, put array elements data type under the 'elements_type' key.");
        }

        if (! array_key_exists('available_data_types', $options)) {
            throw new \InvalidArgumentException("Please, put available data types data under the 'available_data_types' key.");
        }
    }
}