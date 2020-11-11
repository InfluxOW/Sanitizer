<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Tests\Fixtures\Divider;

class SanitizerExtendedWithCustomTypesTest extends TestCase
{
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new Sanitizer([Divider::class]);
    }

    /** @test */
    public function it_may_be_extended_with_custom_data_types()
    {
        self::assertContains(Divider::$slug, $this->sanitizer->getAvailableDataTypes());
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_properly_handle_data()
    {
        $field = 'some_divider_field';
        $value = 10;
        $rules = [$field => ['data_type' => Divider::$slug]];

        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($data, $rules);

        self::assertTrue($status);
        self::assertEquals($result[$field], $value / 2);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_properly_handle_data_within_one_type_elements_array()
    {
        $field = 'some_divider_field';
        $value = [10, 6];
        $rules = [$field => ['data_type' => OneTypeElementsArray::$slug, 'elements_type' => Divider::$slug]];

        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($data, $rules);
        var_dump(($result));
        die();
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_throw_an_error_if_unable_to_handle_value()
    {
        $field = 'some_divider_field';
        $value = [10];
        $rules = [$field => ['data_type' => Divider::$slug]];

        $validData = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($validData, $rules);

        self::assertFalse($status);
        self::assertArrayHasKey('message', $result[$field]);
        self::assertEquals($result[$field]['data'], $value);
    }
}
