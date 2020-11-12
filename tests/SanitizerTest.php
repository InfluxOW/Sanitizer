<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\DataTypes\Implementations\Integer;
use Influx\Sanitizer\DataTypes\Implementations\RussianFederalPhoneNumber;
use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Services\DataParsers\Implementations\Json;
use Influx\Sanitizer\Services\DataParsers\Contracts\Invokable;
use Influx\Sanitizer\Tests\Fixtures\EvenInteger;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase
{
    protected $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = new Sanitizer();
    }

    /*
     * FEIP Test Cases
     * */

    /** @test */
    public function it_properly_passes_first_example_test_case()
    {
        [
            'initial_data' => $initialData,
            'valid_data' => $validData,
            'rules' => $rules
        ] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/first_test_case.json'));

        self::assertNotEquals($validData, $initialData);

        $result = $this->sanitizer->sanitize(
            json_encode($initialData, JSON_THROW_ON_ERROR),
            'json',
            $rules
        );

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals($validData, $result[$this->sanitizer::SANITIZED_DATA_KEY]);
    }

    /** @test */
    public function it_properly_passes_second_example_test_case()
    {
        $value = '123абв';
        $rule = [$this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => Integer::$slug];
        $data = json_encode([$value => $rule], JSON_THROW_ON_ERROR); // equals {"123абв":{"sanitizer_data_type":"integer"}}

        $result = $this->sanitizer->sanitize($data);

        self::assertArrayHasKey($this->sanitizer::SANITATION_ERRORS_KEY, $result); // it means sanitation error has been generated
        self::assertEquals($result[$this->sanitizer::SANITATION_ERRORS_KEY][0]['data'], $value); // it means value in sanitation error data equals provided value
    }

    /** @test */
    public function it_properly_passes_third_example_test_case()
    {
        $value = '260557';
        $rule = [$this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE  => RussianFederalPhoneNumber::$slug];
        $data = json_encode([$value => $rule], JSON_THROW_ON_ERROR); // equals {"260557":{"sanitizer_data_type":"russian_federal_phone_number"}}

        $result = $this->sanitizer->sanitize($data);

        self::assertArrayHasKey($this->sanitizer::SANITATION_ERRORS_KEY, $result); // it means sanitation error has been generated
        self::assertEquals($result[$this->sanitizer::SANITATION_ERRORS_KEY][0]['data'], $value); // it means value in sanitation error data equals provided value
    }

    /*
     * My Test Cases
     * */

    /** @test */
    public function valid_data_passes_its_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_data.json'));

        $result = $this->sanitizer->sanitize($data, 'array', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals($data, $result[$this->sanitizer::SANITIZED_DATA_KEY]);
    }

    /** @test */
    public function invalid_data_may_be_prepared_for_validation_so_it_will_pass_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_after_normalization_data.json'));
        ['data' => $validData] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_data.json'));

        self::assertNotEquals($validData, $data);

        $result = $this->sanitizer->sanitize($data, 'array', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITIZED_DATA_KEY, $result);
        self::assertEquals($validData, $result[$this->sanitizer::SANITIZED_DATA_KEY]);
    }

    /** @test */
    public function it_returns_array_of_errors_for_every_invalid_value_if_data_can_not_pass_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/data_causes_errors.json'));

        $result = $this->sanitizer->sanitize($data, 'array', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITATION_ERRORS_KEY, $result);
        self::assertEquals(array_keys($data), array_keys($result[$this->sanitizer::SANITATION_ERRORS_KEY]));
    }

    /** @test */
    public function it_returns_empty_array_if_no_rules_was_provided()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_data.json'));

        $result = $this->sanitizer->sanitize($data);

        self::assertEmpty($result[$this->sanitizer::SANITIZED_DATA_KEY]);
    }

    /** @test */
    public function it_returns_error_when_no_data_was_found_by_specified_rule_key()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_data.json'));
        $invalidKey = 'nonexistent_key';
        $rules = [ $invalidKey => [$this->sanitizer::RULE_KEY_WHERE_DATA_TYPE_SLUG_SHOULD_BE => ['string']]];

        $result = $this->sanitizer->sanitize($data, 'array', $rules);

        self::assertArrayHasKey($this->sanitizer::SANITATION_ERRORS_KEY, $result);
        self::assertContains($invalidKey, array_keys($result[$this->sanitizer::SANITATION_ERRORS_KEY]));
    }

    /** @test */
    public function it_returns_global_error_if_invalid_data_format_was_provided()
    {
        $result = $this->sanitizer->sanitize('data', 'wrong_data_format', []);

        self::assertArrayHasKey($this->sanitizer::GLOBAL_ERRORS_KEY, $result);
        self::assertCount(1, $result[$this->sanitizer::GLOBAL_ERRORS_KEY]);
    }

    /** @test */
    public function it_returns_global_error_when_unable_to_parse_specified_data_format()
    {
        $result = $this->sanitizer->sanitize('not_a_json_data', 'json', []);

        self::assertArrayHasKey($this->sanitizer::GLOBAL_ERRORS_KEY, $result);
        self::assertCount(1, $result[$this->sanitizer::GLOBAL_ERRORS_KEY]);
    }

    /** @test */
    public function it_returns_global_error_when_invalid_rules_was_provided()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/Fixtures/valid_data.json'));

        $result = $this->sanitizer->sanitize($data, 'json', ['integer' => []]);

        self::assertArrayHasKey($this->sanitizer::GLOBAL_ERRORS_KEY, $result);
        self::assertCount(1, $result[$this->sanitizer::GLOBAL_ERRORS_KEY]);
    }

    /** @test */
    public function it_may_be_extended_with_custom_data_types()
    {
        $sanitizer = new Sanitizer([EvenInteger::class]);

        self::assertContains(EvenInteger::$slug, $sanitizer->getAvailableDataTypes());
    }

    /** @test */
    public function it_throws_exception_when_custom_data_type_do_not_implements_validatable_interface()
    {
        $dataType = new class {
            public static $slug = 'test_data_type';
        };

        $this->expectException(\InvalidArgumentException::class);

        new Sanitizer([$dataType]);
    }

    /** @test */
    public function it_may_be_extended_with_custom_parsers()
    {
        $parser = new class implements Invokable {
            public static $slug = 'test_parser';

            public function __invoke($data): array
            {
                return [];
            }
        };
        $sanitizer = new Sanitizer([], [$parser]);

        self::assertContains($parser::$slug, $sanitizer->getAvailableParsers());
    }

    /** @test */
    public function it_throws_exception_when_custom_parser_do_not_implements_invokable_interface()
    {
        $parser = new class {
            public static $slug = 'test_parser';
        };

        $this->expectException(\InvalidArgumentException::class);

        new Sanitizer([], [$parser]);
    }
}
