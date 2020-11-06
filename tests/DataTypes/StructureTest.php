<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Structure;
use Influx\Sanitizer\Exceptions\NormalizationException;
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
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     * @param array $options
     */
    public function it_can_validate_data($data, array $alreadyValid, array $validAfterNormalization, array $options = ['already_valid' => ['structure' => ['key_1' => ['key'], 'key_2' => 'key']]])
    {
        if (in_array('structure', $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data, $options['already_valid']));
        } else {
            self::assertFalse($this->dataType->validate($data, $options['already_valid']));
        }
    }

    /**
     * @test
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     * @param array $options
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid($data, array $alreadyValid, array $validAfterNormalization, array $options)
    {
        if (in_array('structure', $validAfterNormalization, true)) {
            self::assertFalse($this->dataType->validate($data));

            $normalized = $this->dataType->normalize($data);

            self::assertTrue($this->dataType->validate($normalized));
        } else {
            self::assertTrue(true);
        }
    }

    /**
     * @test
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     * @param array $options
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value($data, array $alreadyValid, array $validAfterNormalization, array $options)
    {
        if (! in_array('structure', array_merge($validAfterNormalization, $alreadyValid), true)) {
            $this->expectException(NormalizationException::class);

            $this->dataType->normalize($data);
        } else {
            self::assertTrue(true);
        }
    }
}