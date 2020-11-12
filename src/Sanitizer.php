<?php

namespace Influx\Sanitizer;

use Influx\Sanitizer\DataTypes\Contracts\PreparesForTransmission;
use Influx\Sanitizer\DataTypes\Contracts\PreparesForValidation;
use Influx\Sanitizer\DataTypes\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\ValidationException;
use Influx\Sanitizer\Services\DataParsers\Contracts\Invokable;
use Influx\Sanitizer\Services\Resolver;

class Sanitizer
{
    public const RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE = 'sanitizer_data_type';
    public const GLOBAL_ERRORS_KEY = 'global_errors';
    public const SANITATION_ERRORS_KEY = 'sanitation_errors';
    public const SANITIZED_DATA_KEY = 'sanitized_data';

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
     * Return available data types slugs.
     *
     * @return array
     */
    public function getAvailableDataTypes(): array
    {
        return array_keys($this->dataTypes);
    }

    /**
     * Return available parsers slugs.
     *
     * @return array
     */
    public function getAvailableParsers(): array
    {
        return array_keys($this->parsers);
    }

    /**
     * Sanitize input of specified format with provided rules.
     *
     * @param $input
     * @param array $rules
     * @param string $inputFormat
     * @return array
     */
    public function sanitize($input, string $inputFormat = 'json', array $rules = []): array
    {
        try {
            $data = is_array($input) ? $input : $this->parseInput($input, $inputFormat);
        } catch (\InvalidArgumentException $e) {
            return [self::GLOBAL_ERRORS_KEY => [$e->getMessage()]];
        }

        if ($this->checkIfDataConsistsOfValueToRuleParams($data) && count(func_get_args()) < 3) {
            [$data, $rules] = $this->transformValueToRuleDataIntoValuesAndRulesArrays($data);
        }

        try {
            $this->verifyRules($rules);
        } catch (\InvalidArgumentException $e) {
            return [self::GLOBAL_ERRORS_KEY => [$e->getMessage()]];
        }

        [$sanitizedData, $sanitationErrors] = $this->sanitizeDataBySpecifiedRules($data, $rules);

        return empty($sanitationErrors) ?
            [self::SANITIZED_DATA_KEY => $sanitizedData] :
            [self::SANITATION_ERRORS_KEY => $sanitationErrors];
    }

    /**
     * Merge default data types with custom ones, verifies them and sets to instance.
     *
     * @param array $customDataTypes
     */
    private function setDataTypes(array $customDataTypes): void
    {
        $dataTypes = array_merge($this->getDefaultDataTypes(), transform_classes_as_slug_to_classname($customDataTypes));
        $contract = Validatable::class;

        if (check_array_elements_implements_interface($dataTypes, $contract)) {
            $this->dataTypes = $dataTypes;

            return;
        }

        throw new \InvalidArgumentException("Some provided data types are not resolving '{$contract}' contract. Please, fix it.");
    }

    /**
     * Return default library data types.
     *
     * @return array
     */
    private function getDefaultDataTypes()
    {
        return parse_directory_classes_as_slug_to_classname(
            __DIR__ . '/DataTypes/Implementations',
            'Influx\\Sanitizer\\DataTypes\\Implementations\\'
        );
    }

    /**
     * Merge default parsers with custom ones, verifies them and sets to instance.
     *
     * @param array $customParsers
     */
    private function setParsers(array $customParsers): void
    {
        $parsers = array_merge($this->getDefaultParsers(), transform_classes_as_slug_to_classname($customParsers));

        if (check_array_elements_implements_interface($parsers, Invokable::class)) {
            $this->parsers = $parsers;

            return;
        }

        throw new \InvalidArgumentException("Please, use invokable parsers.");
    }

    /**
     * Return default library parsers.
     *
     * @return array
     */
    private function getDefaultParsers()
    {
        return parse_directory_classes_as_slug_to_classname(
            __DIR__ . '/Services/DataParsers/Implementations',
            'Influx\\Sanitizer\\Services\\DataParsers\\Implementations\\'
        );
    }

    /**
     * Parse data using special data format parser.
     *
     * @param $data
     * @param string $dataFormat
     * @return array
     */
    private function parseInput($data, string $dataFormat): array
    {
        $parser = $this->resolver->getParserInstance($dataFormat);

        return $parser($data);
    }

    /**
     * Check if data has been passed in as $value => $rule, i.e. without field names.
     *
     * @param array $data
     * @return bool
     */
    private function checkIfDataConsistsOfValueToRuleParams(array $data): bool
    {
        foreach ($data as $value => $rule) {
            if (in_array($rule, $this->getAvailableDataTypes(), true)) {
                continue;
            }

            if (! is_array($rule)) {
                return false;
            }

            if (! array_key_exists(self::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE, $rule)) {
                continue;
            }

            foreach ($rule as $fieldname => $dataType) {
                if (! in_array($dataType, $this->getAvailableDataTypes(), true)) {
                    return false;
                }
            }
        }



        return true;
    }

    /**
     * Transform $value => $rule data into two arrays:
     * array of values and array of rules.
     *
     * @param array $data
     * @return array[]
     */
    private function transformValueToRuleDataIntoValuesAndRulesArrays(array $data)
    {
        $values = [];
        $rules = [];

        foreach ($data as $value => $rule) {
            $values[] = $value;
            $rules[] = $rule;
        }

        return [$values, $rules];
    }

    /**
     * Verify that rules has necessary keys.
     *
     * @param array $rules
     */
    private function verifyRules(array $rules): void
    {
        $key = self::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE;

        foreach ($rules as $parameter => $rule) {
            if (is_array($rule) && array_key_exists($key, $rule)) {
                continue;
            }

            throw new \InvalidArgumentException("Please, put data type under the '$key' key in the '$parameter' rule.");
        }
    }

    /**
     * Sanitize data using provided rules.
     *
     * @param array $data
     * @param array $rules
     * @return array[]
     */
    private function sanitizeDataBySpecifiedRules(array $data, array $rules)
    {
        $sanitizedData = [];
        $sanitationErrors = [];

        foreach ($rules as $parameter => $rule) {
            if (array_key_exists($parameter, $data)) {
                try {
                    $sanitizedData[$parameter] = $this->applyRule($rule, $data[$parameter]);
                } catch (\InvalidArgumentException | ValidationException $e) {
                    $sanitationErrors[$parameter] = ['message' => $e->getMessage(), 'data' => $data[$parameter], 'rule' => $rule];
                }

                continue;
            }

            $sanitationErrors[$parameter] = "Unable to find specified key in the provided data.";
        }

        return [$sanitizedData, $sanitationErrors];
    }

    /**
     * Apply specified rule to the data.
     *
     * @param array $rule
     * @param $data
     * @return mixed
     * @throws \Influx\Sanitizer\Exceptions\ValidationException
     */
    private function applyRule(array $rule, $data)
    {
        $dataType = $this->resolver->getDataTypeInstance($rule[self::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE]);
        $options = array_unset_keys($rule, [self::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE]);

        if ($dataType instanceof PreparesForValidation) {
            $data = $dataType->prepareForValidation($data, $options);
        }

        $isDataValid = $dataType->validate($data, $options);

        if ($isDataValid) {
            return $dataType instanceof PreparesForTransmission ? $dataType->prepareForTransmission($data, $options) : $data;
        }

        throw new ValidationException($dataType->getValidationErrorMessage());
    }
}
