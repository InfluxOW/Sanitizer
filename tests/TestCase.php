<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function basicData()
    {
        return [
            [
                'data' => 'test',
                'already_valid' => [Str::$slug],
                'valid_after_normalization' => [],
            ],
            [
                'data' => '123456test',
                'already_valid' => [Str::$slug],
                'valid_after_normalization' => [],
            ],
            [
                'data' => '79242668541',
                'already_valid' => [RussianFederalPhoneNumber::$slug, Str::$slug],
                'valid_after_normalization' => [Integer::$slug, Double::$slug],
            ],
            [
                'data' => 79242668541,
                'already_valid' => [RussianFederalPhoneNumber::$slug, Integer::$slug],
                'valid_after_normalization' => [Double::$slug, Str::$slug],
            ],
            [
                'data' => '8 (950) 288-56-23',
                'already_valid' => [Str::$slug],
                'valid_after_normalization' => [RussianFederalPhoneNumber::$slug],
            ],
            [
                'data' => 123456.05,
                'already_valid' => [Double::$slug],
                'valid_after_normalization' => [Integer::$slug, Str::$slug],
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
                'valid_after_normalization' => [Str::$slug],
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
