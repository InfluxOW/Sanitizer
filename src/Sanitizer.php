<?php

use Influx\Sanitizer\DataTypes\DataType;
use Influx\Sanitizer\DataTypes\FloatDT;
use Influx\Sanitizer\DataTypes\IntegerDT;
use Influx\Sanitizer\DataTypes\OneTypeElementsArrayDT;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumberDT;
use Influx\Sanitizer\DataTypes\StringDT;
use Influx\Sanitizer\DataTypes\StructureDT;

class Sanitizer
{
    protected $data;
    protected $rules;
    protected $dataTypes = [
        'string' => StringDT::class,
        'integer' => IntegerDT::class,
        'float' => FloatDT::class,
        'russian_federal_phone_number' => RussianFederalPhoneNumberDT::class,
        'one_type_elements_array' => OneTypeElementsArrayDT::class,
        'structure' => StructureDT::class,
    ];

    public function __construct(array $data, array $rules, array $customDataTypes = [])
    {
        $this->data = $data;
        $this->rules = $rules;
        $this->dataTypes = $this->mergeDataTypes($customDataTypes);
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
}