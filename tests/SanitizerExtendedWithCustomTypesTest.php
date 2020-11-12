<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\DataTypes\Implementations\OneTypeElementsArray;
use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Tests\Fixtures\EvenInteger;

class SanitizerExtendedWithCustomTypesTest extends TestCase
{
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new Sanitizer([EvenInteger::class]);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_properly_handle_data()
    {
        $field = 'some_even_integer_field';
        $value = 10;
        $rules = [$field => [$this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => EvenInteger::$slug]]; // equals ['some_even_integer_field' => ['sanitizer_data_type' => 'even_integer']]
        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);

        $result = $this->sanitizer->sanitize($data, 'json', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals($result[$this->sanitizer::SANITIZED_DATA_KEY][$field], $value / 2);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_properly_handle_data_within_one_type_elements_array()
    {
        $field = 'some_even_integer_field';
        $value = [10, 6];
        $rules = [
            $field => [
                $this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => OneTypeElementsArray::$slug,
                'elements_type' => EvenInteger::$slug
            ]
        ];
        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);

        $result = $this->sanitizer->sanitize($data, 'json', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals([5, 3], $result[$this->sanitizer::SANITIZED_DATA_KEY][$field]);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_properly_handle_empty_array_data_within_one_type_elements_array()
    {
        $field = 'some_even_integer_field';
        $value = [];
        $rules = [
            $field => [
                $this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => OneTypeElementsArray::$slug,
                'elements_type' => EvenInteger::$slug
            ]
        ];
        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);

        $result = $this->sanitizer->sanitize($data, 'json', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals([], $result[$this->sanitizer::SANITIZED_DATA_KEY][$field]);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_throw_an_error_if_unable_to_handle_value()
    {
        $field = 'some_even_integer_field';
        $value = [11, 2, 4];
        $rules = [
            $field => [
                $this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => OneTypeElementsArray::$slug,
                'elements_type' => EvenInteger::$slug
            ]
        ];
        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);

        $result = $this->sanitizer->sanitize($data, 'json', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITATION_ERRORS_KEY, $result);
        self::assertEquals($value, $result[$this->sanitizer::SANITATION_ERRORS_KEY][$field]['data']);
    }
}
