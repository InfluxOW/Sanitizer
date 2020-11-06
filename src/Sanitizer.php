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
use Influx\Sanitizer\Services\DataParsers\Json;
use Influx\Sanitizer\Services\Resolver;

class Sanitizer
{
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
    protected Resolver $resolver;

    public function __construct(array $customDataTypes = [], array $customParsers = [])
    {
        $this->dataTypes = $this->mergeDataTypes($customDataTypes);
        $this->parsers = $this->mergeParsers($customParsers);
        $this->resolver = new Resolver($this->dataTypes, $this->parsers);
    }

    public function getAvailableDataTypes(): array
    {
        return array_keys($this->dataTypes);
    }

    public function getAvailableParsers(): array
    {
        return array_keys($this->parsers);
    }

    public function sanitize($input, array $rules, string $inputFormat = 'json'): array
    {
        $data = $this->parseInput($input, $inputFormat);
        $rules = $this->parseRules($rules);
        $result = [];
        $errors = [];

        foreach ($rules as $parameter => $rule) {
            if (array_key_exists($parameter, $data)) {
                try {
                    $result[$parameter] = $this->applyRule($rule, $data[$parameter]);
                } catch (NormalizationException | ValidationException $e) {
                    $errors[$parameter] = ['message' => $e->getMessage(), 'data' => $data[$parameter], 'rule' => $rule];
                }

                continue;
            }

            throw new \InvalidArgumentException("Unable to find key '$parameter' in the provided data.");
        }

        return empty($errors) ? $result : $errors;
    }

    private function mergeDataTypes(array $customDataTypes): array
    {
        foreach ($customDataTypes as $dataType) {
            if ($dataType instanceof Validatable) {
                continue;
            }

            throw new InvalidArgumentException("Custom data type '{$dataType}' is not resolving Validatable contract. Please, fix it.");
        }

        return array_merge($this->dataTypes, $customDataTypes);
    }

    private function mergeParsers(array $customParsers): array
    {
        return array_merge($this->parsers, $customParsers);
    }

    private function applyRule($rule, $datum)
    {
        $dataType = $this->resolver->getDataTypeInstance($rule['data_type']);
        $options = array_unset_keys($rule, ['data_type']);

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

    private function parseInput($data, string $dataFormat): array
    {
        if (is_array($data)) {
            return $data;
        }

        $parser = $this->resolver->getParserInstance($dataFormat);

        return $parser($data);
    }
}