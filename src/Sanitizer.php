<?php

use Influx\Sanitizer\DataTypes\DataType;
use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\DataTypes\Structure;

class Sanitizer
{
    protected $data;
    protected $rules;
    protected $dataTypes = [
        'string' => Str::class,
        'integer' => Integer::class,
        'float' => Double::class,
        'russian_federal_phone_number' => RussianFederalPhoneNumber::class,
        'one_type_elements_array' => OneTypeElementsArray::class,
        'structure' => Structure::class,
    ];

    public function __construct(string $data, array $rules, array $customDataTypes = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->dataTypes = $this->resolveDataTypes($customDataTypes);
    }

    private function resolveDataTypes($customDataTypes)
    {
        foreach ($customDataTypes as $dataType) {
            if ($dataType instanceof DataType) {
                continue;
            }

            throw new InvalidArgumentException("{$dataType} is not resolving DataType contract. Please, fix it.");
        }

        return array_merge($this->dataTypes, $customDataTypes);
    }
}