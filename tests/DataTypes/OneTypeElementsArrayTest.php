<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\Exceptions\NormalizationException;
use PHPUnit\Framework\TestCase;

class OneTypeElementsArrayTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataType = new OneTypeElementsArray();
    }

    /** @test */
    public function integer_values_passes_its_validation()
    {
        self::assertTrue($this->dataType->validate(123));
    }

    /** @test */
    public function non_integer_values_dont_pass_its_validation()
    {
        self::assertFalse($this->dataType->validate([]));
        self::assertFalse($this->dataType->validate('test'));
    }

    /** @test */
    public function it_can_normalize_non_integer_value_so_it_becomes_an_integer()
    {
        $normalizableValue = '123456';
        self::assertFalse($this->dataType->validate($normalizableValue));

        $normalizedValue = $this->dataType->normalize($normalizableValue);
        self::assertTrue($this->dataType->validate($normalizedValue));
    }

    /** @test */
    public function it_throws_an_error_when_unable_to_normalize_a_non_string_value()
    {
        $this->expectException(NormalizationException::class);

        $this->dataType->normalize('123test');
    }

    /** @test */
    public function it_knows_its_normalization_error_message()
    {
        $this->expectExceptionMessage($this->dataType->getNormalizationErrorMessage());

        $this->dataType->normalize([]);
    }

    /** @test */
    public function it_()
    {
        $data = [1, 2, 3, 4, 5];
        $supported = ['integer' => Integer::class,  'string' => Str::class, 'float' => Double::class];
        $options = ['name' => 'integer', 'available_data_types' => $supported];

        $dt = new OneTypeElementsArray($array, $type, $supported);
        $this->assertTrue($dt->validate());
    }
}