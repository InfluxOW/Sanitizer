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
     * @dataProvider oneTypeElementsArrayData
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
     * @dataProvider oneTypeElementsArrayData
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     * @param array $options
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid($data, array $alreadyValid, array $validAfterNormalization, array $options = [])
    {
        if (in_array('one_type_elements_array', $validAfterNormalization, true)) {
            self::assertFalse($this->dataType->validate($data, $options['valid_after_normalization']));

            $normalized = $this->dataType->normalize($data, $options['valid_after_normalization']);

            self::assertTrue($this->dataType->validate($normalized, $options['valid_after_normalization']));
        } else {
            try {
                self::assertFalse($this->dataType->validate($data));
            } catch (\Exception $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }
    }

    /**
     * @test
     * @dataProvider oneTypeElementsArrayData
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value($data, array $alreadyValid, array $validAfterNormalization)
    {
        if (! in_array('one_type_elements_array', array_merge($validAfterNormalization, $alreadyValid), true)) {
            $this->expectException(NormalizationException::class);

            $this->dataType->normalize($data);
        } else {
            $this->markTestSkipped('This part was tested in the previous test.');
        }
    }

    /** @test */
    public function it_throws_an_error_when_elements_type_was_not_provided()
    {
        
    }

    /** @test */
    public function it_throws_an_error_when_elements_type_is_one_type_elements_array()
    {
        
    }

    /** @test */
    public function it_throws_an_error_when_resolver_was_not_provided()
    {
        
    }
}