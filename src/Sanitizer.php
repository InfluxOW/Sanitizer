<?php

namespace Influx\Sanitizer;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Exceptions\ValidationException;
use Influx\Sanitizer\Services\DataParsers\Contracts\Invokable;
use Influx\Sanitizer\Services\Resolver;

class Sanitizer
{
    protected $dataTypes;
    protected $parsers;
    protected Resolver $resolver;

    public function __construct(array $customDataTypes = [], array $customParsers = [])
    {
        $this->setDataTypes($customDataTypes);
        $this->setParsers($customParsers);
        $this->resolver = new Resolver($this->dataTypes, $this->parsers);
    }

    /**
     * Returns available data types slugs.
     *
     * @return array
     */
    public function getAvailableDataTypes(): array
    {
        return array_keys($this->dataTypes);
    }

    /**
     * Returns available parsers slugs.
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
        $result = [];
        $errors = [];

        try {
            $data = $this->parseInput($input, $inputFormat);
            $this->verifyRules($rules);
        } catch (\Exception | \Error $e) {
            $errors['global'][] = $e->getMessage();
        } finally {
            if (count($errors) === 0) {
                foreach ($rules as $parameter => $rule) {
                    if (array_key_exists($parameter, $data)) {
                        try {
                            $result[$parameter] = $this->applyRule($rule, $data[$parameter]);
                        } catch (NormalizationException | ValidationException | \InvalidArgumentException $e) {
                            $errors[$parameter] = ['message' => $e->getMessage(), 'data' => $data[$parameter], 'rule' => $rule];
                        }

                        continue;
                    }

                    $errors[$parameter] = "Unable to find specified key in the provided data.";
                }
            }

            return empty($errors) ?
                ['data' => $result, 'sanitation_passed' => true] :
                ['data' => $errors, 'sanitation_passed' => false];
        }
    }

    /**
     * Merges default data types with custom ones, verifies them and sets to instance.
     *
     * @param array $customDataTypes
     */
    private function setDataTypes(array $customDataTypes): void
    {
        $dataTypes = array_merge($this->getDefaultDataTypes(), parse_classes_to_slug_classname_way($customDataTypes));

        if (check_array_elements_implements_interface($dataTypes, Validatable::class)) {
            $this->dataTypes = $dataTypes;

            return;
        }

        throw new \InvalidArgumentException("Some provided data types are not resolving Validatable contract. Please, fix it.");
    }

    /**
     * Merges default parsers with custom ones, verifies them and sets to instance.
     *
     * @param array $customParsers
     */
    private function setParsers(array $customParsers): void
    {
        $parsers = array_merge($this->getDefaultParsers(), parse_classes_to_slug_classname_way($customParsers));

        if (check_array_elements_implements_interface($parsers, Invokable::class)) {
            $this->parsers = $parsers;

            return;
        }

        throw new \InvalidArgumentException("Please, use invokable parsers.");
    }

    /**
     * Applies specified rule to the data.
     * Returns data if it passes validation.
     * Normalizes data if it is normalizable.
     *
     * @param $rule
     * @param $data
     * @return mixed
     * @throws \Influx\Sanitizer\Exceptions\NormalizationException
     * @throws \Influx\Sanitizer\Exceptions\ValidationException
     */
    private function applyRule($rule, $data)
    {
        $dataType = $this->resolver->getDataTypeInstance($rule['data_type']);
        $options = array_unset_keys($rule, ['data_type']);

        $isDataValid = $dataType->validate($data, $options);

        if ($isDataValid) {
            return $data;
        }

        if ($dataType instanceof Normalizable) {
            return $dataType->normalize($data, $options);
        }

        throw new ValidationException($dataType->getValidationErrorMessage());
    }

    /**
     * Verifies that rules has necessary keys.
     *
     * @param array $rules
     */
    private function verifyRules(array $rules): void
    {
        foreach ($rules as $parameter => $rule) {
            if (array_key_exists('data_type', $rule)) {
                continue;
            }

            throw new \InvalidArgumentException("Please, put data type under the 'data_type' key in the '$parameter' rule.");
        }
    }

    /**
     * Parses data using special data format parser.
     *
     * @param $data
     * @param string $dataFormat
     * @return array
     */
    private function parseInput($data, string $dataFormat): array
    {
        if (is_array($data)) {
            return $data;
        }

        $parser = $this->resolver->getParserInstance($dataFormat);

        return $parser($data);
    }

    /**
     * Returns default library data types.
     *
     * @return array
     */
    private function getDefaultDataTypes()
    {
        return parse_directory_classes_to_slug_classname_way(
            __DIR__ . '/DataTypes',
            'Influx\\Sanitizer\\DataTypes\\'
        );
    }

    /**
     * Returns default library parsers.
     *
     * @return array
     */
    private function getDefaultParsers()
    {
        return parse_directory_classes_to_slug_classname_way(
            __DIR__ . '/Services/DataParsers/Classes',
            'Influx\\Sanitizer\\Services\\DataParsers\\Classes\\'
        );
    }
}
