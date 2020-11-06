<?php

namespace Influx\Sanitizer\Tests;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function data()
    {
        return [
            [
                'data' => 'test',
                'already_valid' => ['string'],
                'valid_after_normalization' => [],
            ],
            [
                'data' => '123456test',
                'already_valid' => ['string'],
                'valid_after_normalization' => [],
            ],
            [
                'data' => '79242668541',
                'already_valid' => ['russian_federal_phone_number', 'string'],
                'valid_after_normalization' => ['integer', 'float'],
            ],
            [
                'data' => 79242668541,
                'already_valid' => ['russian_federal_phone_number', 'integer', ],
                'valid_after_normalization' => ['float', 'string'],
            ],
            [
                'data' => '8 (950) 288-56-23',
                'already_valid' => ['string'],
                'valid_after_normalization' => ['russian_federal_phone_number'],
            ],
            [
                'data' => 123456.05,
                'already_valid' => ['float'],
                'valid_after_normalization' => ['integer', 'string'],
            ],
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'already_valid' => ['one_type_elements_array'],
                'valid_after_normalization' => ['one_type_elements_array'],
                'options' => [
                    'already_valid' => ['elements_type' => 'integer'],
                    'valid_after_normalization' => ['elements_type' => 'string']
                ],
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'already_valid' => ['structure'],
                'valid_after_normalization' => [],
                'options' => ['already_valid' => ['structure' => ['key_1' => ['key'], 'key_2' => 'key']]],
            ],
            [
                'data' => function () { },
                'already_valid' => [],
                'valid_after_normalization' => [],
            ],
            [
                'data' => new class { public function __toString() { return 'test'; } },
                'already_valid' => [],
                'valid_after_normalization' => ['string'],
            ],
        ];
    }
}