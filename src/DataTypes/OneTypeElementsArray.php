<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\App;

class OneTypeElementsArray implements Validatable, Normalizable
{
    public static $slug = 'one_type_elements_array';
    public $needsResolverInstance = true;

    public function validate($data, array $options = []): bool
    {
        $this->checkOptions($options);
        $dataType = $options['resolver']->getDataTypeInstance($options['elements_type']);
        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        $correctTypeData = array_filter($data, function ($value) use ($dataType, $options) {
            return $dataType->validate($value, $options);
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
        $dataType = $options['resolver']->getDataTypeInstance($options['elements_type']);
        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        return array_map(function ($value) use ($options, $dataType) {
            try {
                if ($dataType instanceof Normalizable) {
                    return $dataType->normalize($value, $options);
                }
                throw new NormalizationException($this->getNormalizationErrorMessage());
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

        if ($options['elements_type'] === static::$slug) {
            throw new \LogicException("Unable to handle array of nested arrays.");
        }

        if (! array_key_exists('resolver', $options)) {
            throw new \InvalidArgumentException("Please, put resolver instance under the 'resolver' key.");
        }
    }
}