<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Integer;
use PHPUnit\Framework\TestCase;

class IntegerTest extends TestCase
{
    /** @test */
    public function it_checks_if_provided_value_is_an_integer()
    {
        self::assertTrue((new Integer(123))->validate());
        self::assertFalse((new Integer([]))->validate());
    }

    /** @test */
    public function it_can_normalize_non_integer_value_so_it_becomes_an_integer()
    {
        $normalizableValue = 123456;
        self::assertFalse((new Integer($normalizableValue))->validate());
    }
}