<?php

namespace Influx\Sanitizer;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Exceptions\ValidationException;
use Influx\Sanitizer\Services\Resolver;

class Sanitizer
{
    protected Resolver $resolver;

    public function __construct(array $customDataTypes = [], array $customParsers = [])
    {
        $this->dataTypes = $this->mergeDataTypes($customDataTypes);
        $this->parsers = $this->mergeParsers($customParsers);
        $this->resolver = new Resolver($this->dataTypes, $this->parsers);
    }

    /**
     * Returns available data types.
     *
     * @return array
     */
    public function getAvailableDataTypes(): array
    {
        return array_keys($this->dataTypes);
    }

    /**
     * Returns available parsers.
     *
     * @return array
     */
    public function getAvailableParsers(): array
    {
        return array_keys($this->parsers);
    }

    /**
     * Sanitizes input of specified format with provided rules.
     *
     * @param $input
     * @param array $rules
     * @param string $inputFormat
     * @return array
     */
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
        $dataTypes = array_merge($this->getDefaultDataTypes(), $customDataTypes);

        foreach ($dataTypes as $dataType) {
            if (in_array(Validatable::class, class_implements($dataType), true)) {
                continue;
            }

            throw new \InvalidArgumentException("Data type '{$dataType}' is not resolving Validatable contract. Please, fix it.");
        }

        return $dataTypes;
    }

    private function mergeParsers(array $customParsers): array
    {
        $parsers = array_merge($this->getDefaultParsers(), $customParsers);

        foreach ($parsers as $parser) {
            if (method_exists($parser, '__invoke')) {
                continue;
            }

            throw new \InvalidArgumentException("Please, use invokable parsers.");
        }

        return $parsers;
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

    private function getDefaultDataTypes()
    {
        return parse_directory_classes_in_slug_classname_manner(__DIR__ . '/DataTypes', 'Influx\\Sanitizer\\DataTypes\\');
    }

    private function getDefaultParsers()
    {
        return parse_directory_classes_in_slug_classname_manner(__DIR__ . '/Services/DataParsers', 'Influx\\Sanitizer\\Services\\DataParsers\\');
    }
}