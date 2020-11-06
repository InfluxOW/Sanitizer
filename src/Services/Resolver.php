<?php

namespace Influx\Sanitizer\Services;

use Influx\Sanitizer\Contracts\Validatable;

class Resolver
{
    public function getDataTypeInstance(string $dataType, array $availableDataTypes): Validatable
    {
        if (! array_key_exists($dataType, $availableDataTypes)) {
            throw new \InvalidArgumentException("Unable to find specified data type in the available data types list.");
        }

        return new $this->dataTypes[$rule['data_type']]();
    }

    public function getDataTypeOptions(Validatable $dataType, array $rule): array
    {
        $options = array_diff_key($rule, ['data_type']);

        if (isset($dataType->needsAvailableDataTypesList) && $dataType->needsAvailableDataTypesList) {
            $options['available_data_types'] = $this->dataTypes;
        }

        return $options;
    }


}