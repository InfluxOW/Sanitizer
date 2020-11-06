<?php

namespace Influx\Sanitizer\Services;

use Influx\Sanitizer\Contracts\Validatable;

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
            $dataTypeClass = $this->dataTypes[$dataType];

            if (isset($dataTypeClass::$needsResolverInstance) && $dataTypeClass::$needsResolverInstance) {
                return new $dataTypeClass($this);
            }

            return new $dataTypeClass();
        }

        throw new \InvalidArgumentException("Unable to find specified data type in the available data types list.");
    }

    public function getParserInstance(string $dataFormat)
    {
        if (array_key_exists($dataFormat, $this->parsers) && method_exists($this->parsers[$dataFormat], '__invoke')) {
            return new $this->parsers[$dataFormat];
        }

        throw new \InvalidArgumentException("Unable to find a parser for the specified data format '$dataFormat'.");
    }
}