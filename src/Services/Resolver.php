<?php

namespace Influx\Sanitizer\Services;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\NeedsAnotherDataTypeInstance;

class Resolver
{
    protected array $dataTypes;
    protected array $parsers;

    public function __construct(array $dataTypes = [], array $parsers = [])
    {
        $this->dataTypes = $dataTypes;
        $this->parsers = $parsers;
    }

    public function getDataTypeInstance(string $dataType): Validatable
    {
        if (array_key_exists($dataType, $this->dataTypes)) {
            return new $this->dataTypes[$dataType]();
        }

        throw new \InvalidArgumentException("Unable to find specified data type in the available data types list.");
    }

    public function getDataTypeOptions(Validatable $dataType, array $rule): array
    {
        if (array_key_exists('elements_type', $rule) && ! array_key_exists('needed_data_types', $rule)) {
            $rule['needed_data_types'][] = $rule['elements_type'];
        }

        if (class_uses_trait(get_class($dataType), NeedsAnotherDataTypeInstance::class)) {
            if (array_key_exists('needed_data_types', $rule)) {
                foreach ($rule['needed_data_types'] as $neededDataType) {
                    $rule['needed_data_types'][$neededDataType] = $this->getDataTypeInstance($neededDataType);
                }
            }

            throw new \InvalidArgumentException("If your data type needs another data types instances, please, put their names under 'needed_data_types' key.");
        }

        return array_diff_key($rule, ['data_type']);
    }

    public function getParserInstance(string $dataFormat)
    {
        if (array_key_exists($dataFormat, $this->parsers) && method_exists($this->parsers[$dataFormat], '__invoke')) {
            return new $this->parsers[$dataFormat];
        }

        throw new \InvalidArgumentException("Unable to find a parser for the specified data format '$dataFormat'.");
    }
}