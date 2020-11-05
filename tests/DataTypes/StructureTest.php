<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Structure;
use PHPUnit\Framework\TestCase;

class StructureTest extends TestCase
{
    /** @test */
    public function it_()
    {
        $data = [
            'foo' => [
                'key_1' => 'value',
                'key_2' => [
                    'key_2_1' => 'value',
                    'key_2_2' => 'value',
                ],
            ],
            'bar' => 2,
            'baz' => 3,
        ];

        $keys = [
            'foo' => [
                'key_1',
                'key_2' => [
                    'key_2_1', 'key_2_2'
                ],
            ],
            'bar',
            'baz',
        ];

        $dt = new Structure($data, $keys);
        self::assertTrue($dt->validate());
    }
}