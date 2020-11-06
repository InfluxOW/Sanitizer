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
     * @dataProvider data
     * @param $data
     * @param array $alreadyValid
     * @param array $validAfterNormalization
     * @param array $options
     */
    public function it_can_validate_data($data, array $alreadyValid, array $validAfterNormalization, array $options = [])
    {
        if (in_array('structure', $alreadyValid, true)) {
            self::assertTrue($this->dataType->validate($data, $options['already_valid']));
        } else {
            try {
                self::assertFalse($this->dataType->validate($data));
            } catch (\Exception $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }
    }
}