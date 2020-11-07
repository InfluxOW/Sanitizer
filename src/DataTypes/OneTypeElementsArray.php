<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\App;
use Influx\Sanitizer\Services\Resolver;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class OneTypeElementsArray implements Validatable, Normalizable
{
    use HasDefaultNormalizationErrorMessage;
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

    public function normalize($data, array $options = [])
    {
        $this->verifyData($data);
        $this->verifyOptions($options);

        $dataType = $this->resolver->getDataTypeInstance($options['elements_type']);

        if (! $dataType instanceof Normalizable) {
            throw new NormalizationException("Unable to normalize specified data type.");
        }

        $options = array_unset_keys($options, ['resolver', 'elements_type']);

        return array_map(function ($value) use ($options, $dataType) {
            try {
                return $dataType->normalize($value, $options);
            } catch (NormalizationException $e) {
                throw new NormalizationException($this->getNormalizationErrorMessage());
            }
        }, $data);
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

    private function verifyData($data): void
    {
        if (is_array($data)) {
            return;
        }

        throw new \InvalidArgumentException("Unable to handle non array structures.");
    }
}
