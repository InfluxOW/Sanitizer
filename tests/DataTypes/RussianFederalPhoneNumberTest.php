<?php

namespace Influx\Sanitizer\Tests\DataTypes;

use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Tests\TestCase;

class RussianFederalPhoneNumberTest extends TestCase
{
    protected $dataType;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dataType = new RussianFederalPhoneNumber();
    }

    /** @test
     * @dataProvider basicData
     * @param $data
     * @param array $alreadyValid
     */
    public function it_can_validate_data($data, array $alreadyValid)
    {
        try {
            $data = (string) $data;
        } catch (\Exception | \Error $e) {
            $this->markTestSkipped('It tests in the another test.');
        }

        if (in_array('russian_federal_phone_number', $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data));
        } else {
            self::assertFalse($this->dataType->validate($data));
        }
    }

    /**
     * @test
     * @dataProvider basicData
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid($data, array $alreadyValid, array $validAfterNormalization)
    {
        if (in_array('russian_federal_phone_number', $validAfterNormalization, true)) {
            self::assertFalse($this->dataType->validate($data));

            $normalized = $this->dataType->normalize($data);

            self::assertTrue($this->dataType->validate($normalized));
        } else {
            $this->markTestSkipped('This part tests in the next test.');
        }
    }

    /**
     * @test
     * @dataProvider basicData
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value($data, array $alreadyValid, array $validAfterNormalization)
    {
        if (! in_array('russian_federal_phone_number', array_merge($validAfterNormalization, $alreadyValid), true)) {
            $this->expectException(NormalizationException::class);

            $this->dataType->normalize($data);
        } else {
            $this->markTestSkipped('This part was tested in the previous test.');
        }
    }

    /**
     * @test
     * @dataProvider basicData
     * @param $data
     * @param array $alreadyValid
     */
    public function it_throws_an_error_when_unable_to_validate_a_value($data, array $alreadyValid)
    {
        try {
            (string) $data;
        } catch (\Exception | \Error $e) {
            $this->expectException(\InvalidArgumentException::class);

            $this->dataType->validate($data);
        }

        $this->markTestSkipped('This part was tested in the previous test.');
    }
}