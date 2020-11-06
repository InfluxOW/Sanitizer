<?php

namespace Influx\Sanitizer\Services;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\NeedsAvailableDataTypesList;

class Resolver
{
    protected array $dataTypes;

    public function __construct(array $dataTypes)
    {
        $this->dataTypes = $dataTypes;
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
        if (class_uses_trait($dataType, NeedsAvailableDataTypesList::class)) {
            $rule['available_data_types'] = $this->dataTypes;
        }

        return array_diff_key($rule, ['data_type']);
    }


}