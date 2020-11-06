<?php

namespace Influx\Sanitizer\Tests\Sanitizer;

use Influx\Sanitizer\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    /** @test */
    public function it_can_sanitize_data()
    {
        $data = json_encode(['key' => ['key_1' => ['value' => 'key'], 'key_2' => ['value' => 'key'], 'key_3' => ['value' => 'key']]], JSON_THROW_ON_ERROR);
//        $rules = ['foo' => ['data_type' => 'integer'], 'bar' => ['data_type' => 'string'], 'baz' => ['data_type' => 'russian_federal_phone_number']];
        $rules = ['key' => ['data_type' => 'one_type_elements_array', 'elements_type' => 'structure', 'keys' => ['value']]];
        $sanitizer = (new Sanitizer())->sanitize($data, $rules);
        print_r($sanitizer);

    }
}