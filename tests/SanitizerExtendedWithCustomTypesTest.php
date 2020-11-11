<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Tests\Fixtures\AlphaDash;

class SanitizerExtendedWithCustomTypesTest extends TestCase
{
    protected $customDataType;
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new Sanitizer([AlphaDash::class]);
    }

    /** @test */
    public function it_may_be_extended_with_custom_data_types()
    {
        self::assertContains(AlphaDash::$slug, $this->sanitizer->getAvailableDataTypes());
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_validate_data()
    {
        $field = 'some_alpha_dash_field';
        $value = 'it_should_work_fine_123-';
        $rules = [$field => ['data_type' => AlphaDash::$slug]];

        $data = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($data, $rules);

        self::assertTrue($status);
        self::assertEquals($result[$field], $value);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_normalize_data()
    {
        $field = 'some_alpha_dash_field';
        $value = '!@#it_should_work_fine_123-**^%%%%$';
        $normalizedValue = 'it_should_work_fine_123-';
        $rules = [$field => ['data_type' => AlphaDash::$slug]];

        $validData = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($validData, $rules);

        self::assertTrue($status);
        self::assertEquals($result[$field], $normalizedValue);
    }

    /** @test */
    public function custom_data_type_within_sanitizer_can_throw_an_error_if_unable_to_handle_value()
    {
        $field = 'some_alpha_dash_field';
        $value = ['!@#it_should_work_fine_123-**^%%%%$'];
        $rules = [$field => ['data_type' => AlphaDash::$slug]];

        $validData = json_encode([$field => $value], JSON_THROW_ON_ERROR);
        ['sanitation_passed' => $status, 'data' => $result] = $this->sanitizer->sanitize($validData, $rules);

        self::assertFalse($status);
        self::assertArrayHasKey('message', $result[$field]);
        self::assertEquals($result[$field]['data'], $value);
    }
}
