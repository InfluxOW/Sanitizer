<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\DataTypes\Structure;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Services\Resolver;
use Influx\Sanitizer\Tests\TestCase;

class OneTypeElementsArrayTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $resolver = new Resolver([
            'string' => Str::class,
            'integer' => Integer::class,
            'float' => Double::class,
            'russian_federal_phone_number' => RussianFederalPhoneNumber::class,
            'one_type_elements_array' => OneTypeElementsArray::class,
            'structure' => Structure::class,
        ]);
        $this->dataType = new OneTypeElementsArray($resolver);
    }

    /** @test
     * @dataProvider validationData
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
     * @dataProvider normalizationData
     * @param array $data
     * @param array $options
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid(array $data, array $options)
    {
        self::assertFalse($this->dataType->validate($data, $options));

        $normalized = $this->dataType->normalize($data, $options);

        self::assertTrue($this->dataType->validate($normalized, $options));
    }

    /**
     * @test
     * @dataProvider normalizationErrorData
     * @param array $data
     * @param array $options
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value(array $data, array $options)
    {
        $this->expectException(NormalizationException::class);

        $this->dataType->normalize($data, $options);
    }

    /** @test */
    public function it_throws_an_error_during_validation_when_elements_type_was_not_provided()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->validate($data, $options);
    }

    /** @test */
    public function it_throws_an_error_during_normalization_when_elements_type_was_not_provided()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = [];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->normalize($data, $options);
    }

    /** @test */
    public function it_throws_an_error_during_validation_when_elements_type_is_one_type_elements_array()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = ['elements_type' => 'one_type_elements_array'];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->validate($data, $options);
    }

    /** @test */
    public function it_throws_an_error_during_normalization_when_elements_type_is_one_type_elements_array()
    {
        $data = ['key_1' => [123456], 'key_2' => 123456];
        $options = ['elements_type' => 'one_type_elements_array'];

        $this->expectException(\InvalidArgumentException::class);
        $this->dataType->normalize($data, $options);
    }

    /** @test
     * @dataProvider basicNonArrayData
     * @param $data
     */
    public function it_throws_an_error_when_trying_to_validate_non_array_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    /** @test
     * @dataProvider basicNonArrayData
     * @param $data
     */
    public function it_throws_an_error_when_trying_to_normalize_non_array_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->normalize($data);
    }

    public function validationData()
    {
        return [
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => 'integer'],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['elements_type' => 'structure', 'structure' => ['key']],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => 'string'],
                'expected' => false,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['elements_type' => 'structure', 'structure' => ['key_1', 'key_2']],
                'expected' => false,
            ],
        ];
    }

    public function normalizationData()
    {
        return [
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => 'string'],
            ],
            [
                'data' => ['key_1' => 123456, 'key_2' => 123456],
                'options' => ['elements_type' => 'float'],
            ],
        ];
    }

    public function normalizationErrorData()
    {
        return [
            [
                'data' => ['key_1' => [123456], 'key_2' => 123456],
                'options' => ['elements_type' => 'russian_federal_phone_number'],
            ],
            [
                'data' => ['key_1' => [123456], 'key_2' => [123456]],
                'options' => ['elements_type' => 'structure'],
            ],
        ];
    }
}