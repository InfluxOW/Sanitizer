<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class OneTypeElementsArray implements DataType, Normalizable
{
    protected $data;
    protected string $type;
    protected array $supportableTypes;

    public function __construct($data, string $type, array $supportableTypes)
    {
        $this->data = $data;
        $this->type = $type;
        $this->supportableTypes = $supportableTypes;
    }

    public function validate(): bool
    {
        $dataType = $this->getDataType();

        $correctTypeData = array_filter($this->data, function ($value) use ($dataType) {
            return (new $dataType($value))->validate();
        });

        return count($correctTypeData) === count($this->data);
    }

    public function normalize(): DataType
    {
        $dataType = $this->getDataType();

        return array_map(function ($value) use ($dataType) {
            try {
                return (new $dataType($value))->normalize();
            } catch (NormalizationException $e) {
                throw new NormalizationException($this->getErrorMessage());
            }
        }, $this->data);
    }

    public function getErrorMessage($value = null): string
    {
        return "Provided data is not a one type elements array and couldn't be converted to it.";
    }

    public function getData()
    {
        return $this->data;
    }

    private function getDataType()
    {
        if (array_key_exists($this->type, $this->supportableTypes)) {
            return $this->supportableTypes[$this->type];
        }

        throw new \InvalidArgumentException("Specified type is not in the supportable types list.");
    }
}