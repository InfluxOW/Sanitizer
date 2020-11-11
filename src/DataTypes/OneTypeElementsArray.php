<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\HasBeforeValidationHook;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Services\Resolver;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class OneTypeElementsArray implements Validatable, HasBeforeValidationHook
{
    use HasDefaultValidationErrorMessage;

    public static $slug = 'one_type_elements_array';
    private $resolver;

    public function __construct(Resolver $resolver)
    {
        $this->resolver = $resolver;
    }

    public function validate($data, array $options = []): bool
    {
        $this->verifyData($data);
        $this->verifyOptions($options);

        $dataType = $this->resolver->getDataTypeInstance($options['elements_type']);
        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        $correctTypeData = array_filter($data, function ($value) use ($dataType, $options) {
            return $dataType->validate($value, $options);
        });

        return $correctTypeData === $data;
    }

    public function beforeValidation($data, array $options = [])
    {
        $this->verifyData($data);
        $this->verifyOptions($options);

        $dataType = $this->resolver->getDataTypeInstance($options['elements_type']);

        if ($dataType instanceof HasBeforeValidationHook) {
            $options = array_unset_keys($options, ['resolver', 'elements_type']);

            return array_map(function ($value) use ($options, $dataType) {
                try {
                    return $dataType->beforeValidation($value, $options);
                } catch (\InvalidArgumentException $e) {
                    throw new \InvalidArgumentException('Unable to apply before validation action on the provided type of data.');
                }
            }, $data);
        }

        throw new \InvalidArgumentException('To apply before validation action on the data within provided data type it should implement HasBeforeValidationHook interface.');
    }

    private function verifyData($data): void
    {
        if (is_array($data)) {
            return;
        }

        throw new \InvalidArgumentException("Unable to handle non array structures.");
    }

    private function verifyOptions(array $options): void
    {
        if (! array_key_exists('elements_type', $options)) {
            throw new \InvalidArgumentException("Please, put array elements data type under the 'elements_type' key.");
        }

        if ($options['elements_type'] === static::$slug) {
            throw new \InvalidArgumentException("Unable to handle array of nested one type elements arrays.");
        }
    }
}
