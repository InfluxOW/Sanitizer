<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;
use Influx\Sanitizer\Services\Resolver;

class OneTypeElementsArray extends DataType
{
    public static $slug = 'one_type_elements_array';

    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function validate($data, array $options = []): bool
    {
        $this->verifyInput($data, $options);

        $dataType = $this->resolver->getDataTypeInstance($options['elements_type']);
        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        $correctTypeData = array_filter($data, function ($value) use ($dataType, $options) {
            return $dataType->validate($value, $options);
        });

        return $correctTypeData === $data;
    }

    public function prepareForValidation($data, array $options = [])
    {
        $this->verifyInput($data, $options);

        $dataType = $this->resolver->getDataTypeInstance($options['elements_type']);
        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        return array_map(function ($value) use ($options, $dataType) {
            try {
                return $dataType->prepareForValidation($value, $options);
            } catch (\InvalidArgumentException $e) {
                throw new \InvalidArgumentException('Unable to convert provided type of data to one type elements array with specified elements type.');
            }
        }, $data);
    }

    private function verifyInput($data, array $options): void
    {
        if (! is_array($data)) {
            throw new \InvalidArgumentException("Unable to handle non array structures.");
        }

        if (! array_key_exists('elements_type', $options)) {
            throw new \InvalidArgumentException("Please, put array elements data type under the 'elements_type' key.");
        }

        if ($options['elements_type'] === static::$slug) {
            throw new \InvalidArgumentException("Unable to handle array of nested one type elements arrays.");
        }
    }
}
