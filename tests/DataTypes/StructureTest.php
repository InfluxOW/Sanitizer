<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Structure;
use Influx\Sanitizer\Tests\TestCase;

class StructureTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataType = new Structure();
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

    /** @test
     * @dataProvider validationData
     * @param array $data
     */
    public function it_throws_error_during_validation_when_no_structure_was_provided(array $data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    /** @test
     * @dataProvider basicNonArrayData
     * @param $data
     */
    public function it_throws_error_during_validation_when_non_array_data_was_provided($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    public function validationData()
    {
        return [
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['structure' => ['key_1' => ['key'], 'key_2' => ['key']]],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['structure' => ['*' => ['key']]],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['structure' => ['*' => 'key']],
                'expected' => false,
            ],
            [
                'data' => ['key_1' => 'value', 'key_2' => 'value'],
                'options' => ['structure' => ['*' => ['key']]],
                'expected' => false,
            ],
            [
                'data' => [
                    'key_1' => ['*' => ['key' => 'value']],
                    'key_2' => ['*' => ['key' => 'value']],
                ],
                'options' => [
                    'structure' => [
                        'key_1' => ['*' => ['key']],
                        'key_2' => ['*' => ['key']],
                    ]
                ],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['structure' => ['key_1' => ['key'], 'key_2' => ['key']]],
                'expected' => true,
            ],
            [
                'data' => ['key_1' => ['key' => 'value'], 'key_2' => ['key' => 'value']],
                'options' => ['structure' => ['key_1', 'key_2']],
                'expected' => true,
            ],
        ];
    }
}
