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
     * @dataProvider validationData
     * @param $data
     * @param array $alreadyValid
     */
    public function it_can_validate_data($data, array $alreadyValid)
    {
        if (in_array('russian_federal_phone_number', $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data));
        } else {
            self::assertFalse($this->dataType->validate($data));
        }
    }

    /**
     * @test
     * @dataProvider normalizationData
     * @param $data
     */
    public function it_can_normalize_invalid_data_so_it_becomes_valid($data)
    {
        self::assertFalse($this->dataType->validate($data));

        $normalized = $this->dataType->normalize($data);

        self::assertTrue($this->dataType->validate($normalized));
    }

    /**
     * @test
     * @dataProvider normalizationErrorData
     * @param $data
     */
    public function it_throws_an_error_when_unable_to_normalize_a_value($data)
    {
        $this->expectException(NormalizationException::class);

        $this->dataType->normalize($data);
    }

    /**
     * @test
     * @dataProvider validationErrorData
     * @param $data
     */
    public function it_throws_an_error_when_unable_to_validate_a_value($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    public function validationData()
    {
        return array_filter($this->basicData(), function ($datum) {
            if (is_array($datum['data'])) {
                return false;
            }

            try {
                return (string) $datum['data'];
            } catch (\Exception | \Error $e) {
                return false;
            }
        });
    }

    public function validationErrorData()
    {
        return array_filter($this->basicData(), function ($datum) {
            if (is_array($datum['data'])) {
                return true;
            }

            try {
                if ((string) $datum['data']) {
                    return false;
                }
            } catch (\Exception | \Error $e) {
                return true;
            }
        });
    }

    public function normalizationData()
    {
        return array_filter($this->basicData(), function ($datum) {
            return in_array('russian_federal_phone_number', $datum['valid_after_normalization'], true);
        });
    }

    public function normalizationErrorData()
    {
        return array_filter($this->basicData(), function ($datum) {
            return ! in_array('russian_federal_phone_number', array_merge($datum['valid_after_normalization'], $datum['already_valid']), true);
        });
    }
}