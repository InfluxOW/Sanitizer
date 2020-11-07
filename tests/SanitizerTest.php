<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Services\DataParsers\Json;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new Sanitizer();
    }

    /** @test */
    public function valid_data_passes_its_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        self::assertEquals($data, $this->sanitizer->sanitize($data, $rules));
    }

    /** @test */
    public function invalid_data_may_be_normalized_so_it_will_pass_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_after_normalization_data.json'));

        self::assertNotEquals($data, $this->sanitizer->sanitize($data, $rules));

        ['data' => $validData] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        self::assertEquals($validData, $this->sanitizer->sanitize($data, $rules));
    }
}