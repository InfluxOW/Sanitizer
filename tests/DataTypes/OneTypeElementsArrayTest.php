<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Implementations\Double;
use Influx\Sanitizer\DataTypes\Implementations\Integer;
use Influx\Sanitizer\DataTypes\Implementations\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\Implementations\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Implementations\Str;
use Influx\Sanitizer\DataTypes\Implementations\Structure;
use Influx\Sanitizer\Services\Resolver;
use Influx\Sanitizer\Tests\TestCase;

class OneTypeElementsArrayTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $resolver = new Resolver([
            Str::$slug => Str::class,
            Integer::$slug => Integer::class,
            Double::$slug => Double::class,
            RussianFederalPhoneNumber::$slug => RussianFederalPhoneNumber::class,
            OneTypeElementsArray::$slug => OneTypeElementsArray::class,
            Structure::$slug => Structure::class,
        ]);
        $this->dataType = new OneTypeElementsArray($resolver);
    }

    /** @test
     * @dataProvider dataForValidationCheck
     * @param array $data
     * @param array $options
     * @param bool $expected
     */
    public function it_can_validate_data(array $data, array $options, bool $expected)
    {
        self::assertEquals($this->dataType->validate($data, $options), $expected);
    }

    /**
     * @test
     * @dataProvider dataForBeforeValidationCheck
     * @param array $data
     * @param array $options
     */
    public function it_can_process_before_validation_action_so_invalid_data_may_become_valid(array $data, array $options)
    {
        self::assertFalse($this->dataType->validate($data, $options));

        $normalized = $this->dataType->prepareForValidation($data, $options);

        self::assertTrue($this->dataType->validate($normalized, $options));
    }

    /**
     * @test
     * @dataProvider dataForBeforeValidationErrorCheck
     * @param array $data
     * @param array $options
     */
    public function it_throws_an_invalid_argument_exception_when_unable_to_process_before_validation_action_on_provided_data(array $data, array $options)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->prepareForValidation($data, $options);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_during_validation_when_elements_type_was_not_provided()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->validate($data, $options);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_during_before_validation_action_when_elements_type_was_not_provided()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->prepareForValidation($data, $options);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_during_validation_when_elements_type_is_one_type_elements_array()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = ['elements_type' => OneTypeElementsArray::$slug];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->validate($data, $options);
    }

    /** @test */
    public function it_throws_an_invalid_argument_exception_during_normalization_when_elements_type_is_one_type_elements_array()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = ['elements_type' => OneTypeElementsArray::$slug];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->prepareForValidation($data, $options);
    }

    /** @test
     * @dataProvider basicNonArrayData
     * @param $data
     */
    public function it_throws_an_invalid_argument_exception_when_trying_to_validate_non_array_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    /** @test
     * @dataProvider basicNonArrayData
     * @param $data
     */
    public function it_throws_an_invalid_argument_exception_when_trying_to_process_before_validation_action_on_non_array_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->prepareForValidation($data);
    }

    public function dataForValidationCheck()
    {
        return [
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => Integer::$slug],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['elements_type' => Structure::$slug, 'structure' => ['key']],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => Str::$slug],
                'expected' => false,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['elements_type' => Structure::$slug, 'structure' => ['key_1', 'key_2']],
                'expected' => false,
            ],
        ];
    }

    public function dataForBeforeValidationCheck()
    {
        return [
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => Str::$slug],
            ],
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => Double::$slug],
            ],
        ];
    }

    public function dataForBeforeValidationErrorCheck()
    {
        return [
            [
                'data' => ['key_1' => [123456], 'key_2' => [123456]],
                'options' => ['elements_type' => RussianFederalPhoneNumber::$slug],
            ],
            [
                'data' => ['key_1' => '123test', 'key_2' => [123456]],
                'options' => ['elements_type' => Integer::$slug],
            ],
        ];
    }
}
