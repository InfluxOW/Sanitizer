<?php

namespace Influx\Sanitizer;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\DataTypes\DataType;
use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\DataTypes\Structure;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Exceptions\ValidationException;

class Sanitizer
{
    protected array $data;
    protected array $rules;
    protected array $dataTypes = [
        'string' => Str::class,
        'integer' => Integer::class,
        'float' => Double::class,
        'russian_federal_phone_number' => RussianFederalPhoneNumber::class,
        'one_type_elements_array' => OneTypeElementsArray::class,
        'structure' => Structure::class,
    ];

    public function __construct(string $data, array $rules, array $customDataTypes = [])
    {
        $this->data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $this->rules = $rules;
        $this->dataTypes = $this->mergeDataTypes($customDataTypes);
    }

    public function execute(): array
    {
        $result = [];
        $errors = [];

        foreach ($this->data as $key => $value) {
            if (array_key_exists($key, $this->rules)) {
                try {
                    $result[$key] = $this->applyRule($this->rules[$key], $value);
                } catch (\Exception $e) {
                    $errors[$key] = $e->getMessage();
                }

                continue;
            }

            $result[$key] = $value;
        }

        return empty($errors) ? $errors : $result;
    }

    private function applyRule($rule, $value)
    {
        $dt = static::resolveDataTypeInstance($rule, $this->dataTypes);

        if ($dt->needsAvailableDataTypesList) {
            $rule['available_data_types'] = $this->dataTypes;
        }

        $validated = $dt->validate($value, array_diff_key($rule, ['name']));

        if ($validated) {
            return $value;
        }

        if ($dt instanceof Normalizable) {
            return $dt->normalize($value, array_diff_key($rule, ['name']));
        }

        throw new ValidationException($dt->getValidationErrorMessage());
    }

    public static function resolveDataTypeInstance($rule, $availableDataTypes)
    {
        if (! array_key_exists('name', $rule)) {
            throw new \InvalidArgumentException("Please, put data type name under the 'name' key.");
        }

        if (! array_key_exists($rule['name'], $availableDataTypes)) {
            throw new \InvalidArgumentException("Unable to find specified data type name in the available data types list.");
        }

        return new $availableDataTypes[$rule['name']]();
    }

    private function mergeDataTypes($customDataTypes)
    {
        foreach ($customDataTypes as $dataType) {
            if ($dataType instanceof Validatable) {
                continue;
            }

            throw new InvalidArgumentException("{$dataType} is not resolving Validatable contract. Please, fix it.");
        }

        return array_merge($this->dataTypes, $customDataTypes);
    }
}