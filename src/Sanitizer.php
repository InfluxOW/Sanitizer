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
use Influx\Sanitizer\Services\Parsers\Json;

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
    protected array $parsers = [
      'json' => Json::class,
    ];

    public function __construct($data, array $rules, string $dataFormat = 'json')
    {
        $this->data = ['original' => $data, 'format' => $dataFormat];
        $this->rules = $this->parseRules($rules);
    }

    public function sanitize(): array
    {
        $result = [];
        $errors = [];

        $data = $this->parseOriginalData();

        foreach ($data as $key => $datum) {
            if (array_key_exists($key, $this->rules)) {
                try {
                    $result[$key] = $this->applyRule($this->rules[$key], $datum);
                } catch (NormalizationException | ValidationException $e) {
                    $errors[$key] = ['data' => $datum, 'message' => $e->getMessage()];
                } catch (\Exception $e) {
                    $errors[$key] = ['message' => $e->getMessage()];
                }

                continue;
            }
        }

        return empty($errors) ? $result : $errors;
    }

    private function applyRule($rule, $datum)
    {
        $dataType = $this->resolveDataTypeInstance($rule);
        $options = $this->resolveDataTypeOptions($dataType, $rule);

        $validated = $dataType->validate($datum, $options);

        if ($validated) {
            return $datum;
        }

        if ($dataType instanceof Normalizable) {
            return $dataType->normalize($datum, $options);
        }

        throw new ValidationException($dataType->getValidationErrorMessage());
    }

    private function parseRules(array $rules): array
    {
        foreach ($rules as $rule) {
            if (! array_key_exists('data_type', $rule)) {
                throw new \InvalidArgumentException("Please, put data type under the 'data_type' key.");
            }
        }

        return $rules;
    }

    private function parseOriginalData(): array
    {
        $originalData = $this->data['original'];
        $dataFormat = $this->data['format'];

        if (is_array($originalData)) {
            return $originalData;
        }

        if (array_key_exists($dataFormat, $this->parsers)) {
            return new $this->parsers[$dataFormat]($originalData);
        }

        throw new \InvalidArgumentException('Unable to parse provided data.');
    }

    public function addCustomDataTypes(array $customDataTypes): void
    {
        foreach ($customDataTypes as $dataType) {
            if (! $dataType instanceof Validatable) {
                throw new InvalidArgumentException("Custom data type '{$dataType}' is not resolving Validatable contract. Please, fix it.");
            }
        }

        $this->dataTypes = array_merge($this->dataTypes, $customDataTypes);
    }

    public function addCustomParsers(array $parsers): void
    {
        $this->parsers = array_merge($this->parsers, $parsers);
    }
}