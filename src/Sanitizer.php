<?php

namespace Influx\Sanitizer;

use Influx\Sanitizer\DataTypes\DataType;
use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\DataTypes\Structure;

class Sanitizer
{
    protected array $data;
    protected array $rules;
    protected array $dataTypes = [
        'string' => Str::class,
        'integer' => Integer::class,
        'float' => Double::class,
        'russian_federal_phone_number' => RussianFederalPhoneNumber::class,
        'one_type_elements_array' => OneTypeElementsArray::class,
        'structure' => Structure::class,
    ];

    public function __construct(string $data, array $rules, array $customDataTypes = [])
    {
        $this->data = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $this->rules = $rules;
        $this->dataTypes = $this->mergeDataTypes($customDataTypes);
    }

    public function execute(): array
    {
        $result = [];

        foreach ($this->data as $key => $value) {
            $result[$key] = array_key_exists($key, $this->rules) ?
                $this->applyRule($this->rules[$key], $value) :
                $value;
        }

        return $result;
    }

    private function mergeDataTypes($customDataTypes)
    {
        foreach ($customDataTypes as $dataType) {
            if ($dataType instanceof DataType) {
                continue;
            }

            throw new InvalidArgumentException("{$dataType} is not resolving DataType contract. Please, fix it.");
        }

        return array_merge($this->dataTypes, $customDataTypes);
    }

    private function applyRule($rule, $value)
    {
        $dt = Helpers::resolveDataTypeInstance($rule);

    }


}