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
     * @dataProvider validationData
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

    public function validationData()
    {
        return $this->basicData();
    }

    public function normalizationData()
    {
        return array_filter($this->basicData(), function ($datum) {
            return in_array('string', $datum['valid_after_normalization'], true);
        });
    }

    public function normalizationErrorData()
    {
        return array_filter($this->basicData(), function ($datum) {
            return ! in_array('string', array_merge($datum['valid_after_normalization'], $datum['already_valid']), true);
        });
    }
}
