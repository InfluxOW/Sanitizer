<?php

namespace Influx\Sanitizer\Tests\Sanitizer;

use Influx\Sanitizer\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    /** @test */
    public function it_can_sanitize_data()
    {
        $data =  '{"foo": "123sdhsdh", "bar": "asd", "baz": "8 (950) 288-56-23"}';
        $rules = ['foo' => ['name' => 'integers'], 'bar' => ['name' => 'string'], 'baz' => ['name' => 'russian_federal_phone_number']];
        $sanitizer = (new Sanitizer($data, $rules))->execute();
        print_r($sanitizer);

    }
}