<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\Str;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Tests\TestCase;

class StrTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataType = new Str();
    }

    /** @test
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     */
    public function it_can_validate_data($data, array $alreadyValid)
    {
        if (in_array('string', $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data));
        } else {
            self::assertFalse($this->dataType->validate($data));
        }
    }

    /**
     * @test
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid($data, array $alreadyValid, array $validAfterNormalization)
    {
        if (in_array('string', $validAfterNormalization, true)) {
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
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value($data, array $alreadyValid, array $validAfterNormalization)
    {
        if (! in_array('string', array_merge($validAfterNormalization, $alreadyValid), true)) {
            $this->expectException(NormalizationException::class);

            $this->dataType->normalize($data);
        } else {
            self::assertTrue(true);
        }
    }
}