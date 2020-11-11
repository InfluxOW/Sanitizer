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
     * @dataProvider dataForValidationCheck
     * @param $data
     * @param array $alreadyValid
     */
    public function it_can_validate_data($data, array $alreadyValid)
    {
        if (in_array($this->dataType::$slug, $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data));
        } else {
            self::assertFalse($this->dataType->validate($data));
        }
    }

    /**
     * @test
     * @dataProvider dataForBeforeValidationCheck
     * @param $data
     */
    public function it_can_process_before_validation_action_so_invalid_data_may_become_valid($data)
    {
        self::assertFalse($this->dataType->validate($data));

        $normalized = $this->dataType->beforeValidation($data);

        self::assertTrue($this->dataType->validate($normalized));
    }

    /**
     * @test
     * @dataProvider dataForValidationErrorCheck
     * @param $data
     */
    public function it_throws_an_invalid_argument_exception_when_unable_to_validate_provided_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->validate($data);
    }

    /**
     * @test
     * @dataProvider dataForBeforeValidationErrorCheck
     * @param $data
     */
    public function it_throws_an_invalid_argument_exception_when_unable_to_process_before_validation_action_on_provided_data($data)
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->dataType->beforeValidation($data);
    }

    public function dataForValidationCheck()
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

    public function dataForValidationErrorCheck()
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

    public function dataForBeforeValidationCheck()
    {
        return array_filter($this->basicData(), function ($datum) {
            return in_array('russian_federal_phone_number', $datum['valid_after_normalization'], true);
        });
    }

    public function dataForBeforeValidationErrorCheck()
    {
        return array_filter($this->basicData(), function ($datum) {
            return ! in_array('russian_federal_phone_number', array_merge($datum['valid_after_normalization'], $datum['already_valid']), true);
        });
    }
}
