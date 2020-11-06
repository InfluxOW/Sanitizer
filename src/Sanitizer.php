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
use Influx\Sanitizer\Services\Resolver;

class Sanitizer
{
    protected array $data;
    protected array $rules;
    protected Resolver $resolver;
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
        $this->data = ['data' => $data, 'format' => $dataFormat];
        $this->rules = $this->parseRules($rules);
        $this->resolver = new Resolver($this->dataTypes);
    }

    public function addCustomDataTypes(array $customDataTypes): void
    {
        foreach ($customDataTypes as $dataType) {
            if ($dataType instanceof Validatable) {
                continue;
            }

            throw new InvalidArgumentException("Custom data type '{$dataType}' is not resolving Validatable contract. Please, fix it.");
        }

        $this->dataTypes = array_merge($this->dataTypes, $customDataTypes);
        $this->resolver = new Resolver($this->dataTypes);
    }

    public function addCustomParsers(array $parsers): void
    {
        $this->parsers = array_merge($this->parsers, $parsers);
    }

    public function getAvailableDataTypes(): array
    {
        return array_keys($this->dataTypes);
    }

    public function sanitize(): array
    {
        $result = [];
        $errors = [];
        $data = $this->parseOriginalData();

        foreach ($this->rules as $parameter => $rule) {
            if (array_key_exists($parameter, $data)) {
                try {
                    $result[$parameter] = $this->applyRule($rule, $data[$parameter]);
                } catch (NormalizationException | ValidationException $e) {
                    $errors[$parameter] = ['data' => $data[$parameter], 'message' => $e->getMessage()];
                } catch (\Exception $e) {
                    $errors[$parameter] = ['message' => $e->getMessage()];
                }
            }

            throw new \InvalidArgumentException("Unable to find key '$parameter' in the provided data.");
        }

        return empty($errors) ? $result : $errors;
    }

    private function applyRule($rule, $datum)
    {
        $dataType = $this->resolver->getDataTypeInstance($rule['data_type']);
        $options = $this->resolver->getDataTypeOptions($dataType, $rule);

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
        foreach ($rules as $parameter => $rule) {
            if (array_key_exists('data_type', $rule)) {
                continue;
            }

            throw new \InvalidArgumentException("Please, put data type under the 'data_type' key in the '$parameter' rule.");
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
}