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
                } catch (NormalizationException | ValidationException $e) {
                    $errors[$key] = ['data' => $value, 'message' => $e->getMessage()];
                } catch (\Exception $e) {
                    $errors[$key] = ['message' => $e->getMessage()];
                }

                continue;
            }

            $result[$key] = $value;
        }

        return empty($errors) ? $result : $errors;
    }

    private function applyRule($rule, $value)
    {
        $rule['available_data_types'] = $this->dataTypes;
        $dt = static::resolveDataTypeInstance($rule, $this->dataTypes);

        $options = array_diff_key($rule, ['data_type']);
        $validated = $dt->validate($value, $options);

        if ($validated) {
            return $value;
        }

        if ($dt instanceof Normalizable) {
            return $dt->normalize($value, $options);
        }

        throw new ValidationException($dt->getValidationErrorMessage());
    }

    public static function resolveDataTypeInstance($rule)
    {
        if (! array_key_exists('data_type', $rule)) {
            throw new \InvalidArgumentException("Please, put data type under the 'data_type' key.");
        }

        if (! array_key_exists($rule['data_type'], $rule['available_data_types'])) {
            throw new \InvalidArgumentException("Unable to find specified data type in the available data types list.");
        }

        return new $rule['available_data_types'][$rule['data_type']]();
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