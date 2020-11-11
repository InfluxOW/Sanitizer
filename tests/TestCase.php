<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function basicData()
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
                'already_valid' => ['russian_federal_phone_number', 'integer'],
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
                'already_valid' => [],
                'valid_after_normalization' => [],
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'already_valid' => [],
                'valid_after_normalization' => [],
            ],
            [
                'data' => function () {
                },
                'already_valid' => [],
                'valid_after_normalization' => [],
            ],
            [
                'data' => new class {
                    public function __toString()
                    {
                        return 'test';
                    }
                },
                'already_valid' => [],
                'valid_after_normalization' => ['string'],
            ],
        ];
    }

    public function basicNonArrayData()
    {
        return array_filter($this->basicData(), function ($datum) {
            return ! is_array($datum['data']);
        });
    }
}
