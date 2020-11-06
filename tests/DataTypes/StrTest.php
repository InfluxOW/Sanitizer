<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\Exceptions\NormalizationException;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataType = new Str();
    }

    /** @test */
    public function string_values_passes_its_validation()
    {
        self::assertTrue($this->dataType->validate('just a random string'));
    }

    /** @test */
    public function non_string_values_dont_pass_its_validation()
    {
        self::assertFalse($this->dataType->validate([]));
        self::assertFalse($this->dataType->validate(123456));
    }

    /** @test */
    public function it_can_normalize_non_string_value_so_it_becomes_a_string()
    {
        $normalizableValue = 123456;
        self::assertFalse($this->dataType->validate($normalizableValue));

        $normalizedValue = $this->dataType->normalize($normalizableValue);
        self::assertTrue($this->dataType->validate($normalizedValue));
    }

    /** @test */
    public function it_throws_an_error_when_unable_to_normalize_a_non_string_value()
    {
        $this->expectException(NormalizationException::class);

        $this->dataType->normalize([]);
    }

    /** @test */
    public function it_knows_its_normalization_error_message()
    {
        $this->expectExceptionMessage($this->dataType->getNormalizationErrorMessage());

        $this->dataType->normalize([]);
    }
}