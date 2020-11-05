<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Double;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;
use Influx\Sanitizer\DataTypes\Str;
use PHPUnit\Framework\TestCase;

class OneTypeElementsArrayTest extends TestCase
{
    /** @test */
    public function it_()
    {
        $array = [1, 2, 3, 4, 'string'];
        $type = 'integer';
        $supported = ['integer' => Integer::class,  'string' => Str::class, 'float' => Double::class];

        $dt = new OneTypeElementsArray($array, $type, $supported);
        $this->assertTrue($dt->validate());
    }
}