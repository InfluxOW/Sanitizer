<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\Exceptions\NormalizationException;
use PHPUnit\Framework\TestCase;

class StrTest extends TestCase
{
    /** @test */
    public function it_checks_if_provided_value_is_a_string()
    {
        self::assertTrue((new Str('just a random string'))->validate());
        self::assertFalse((new Str([]))->validate());
    }

    /** @test */
    public function it_can_normalize_non_string_value_so_it_becomes_a_string()
    {
        $normalizableValue = 123456;
        self::assertFalse((new Str($normalizableValue))->validate());

        $normalizedValue = (new Str($normalizableValue))->normalize()->getValue();
        self::assertTrue((new Str($normalizedValue))->validate());
    }

    /** @test */
    public function it_throws_an_error_when_unable_to_normalize_a_non_string_value()
    {
        $this->expectException(NormalizationException::class);

        (new Str([]))->normalize();
    }

    /** @test */
    public function it_knows_its_normalization_error_message()
    {
        $str = (new Str([]));

        $this->expectExceptionMessage($str->getErrorMessage());

        $str->normalize();
    }

    /** @test */
    public function it_knows_provided_value()
    {
        $value = 123456;
        $str = (new Str($value));

        self::assertEquals($value, $str->getValue());
    }
}