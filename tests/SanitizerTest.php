<?php

namespace Influx\Sanitizer\Tests;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\DataTypes\Integer;
use Influx\Sanitizer\DataTypes\RussianFederalPhoneNumber;
use Influx\Sanitizer\Sanitizer;
use Influx\Sanitizer\Services\DataParsers\Classes\Json;
use Influx\Sanitizer\Services\DataParsers\Contracts\Invokable;
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
        ['sanitation_passed' => $status] = $this->sanitizer->sanitize($data, $rules);

        self::assertTrue($status);
    }

    /** @test */
    public function invalid_data_may_be_normalized_so_it_will_pass_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_after_normalization_data.json'));
        ['data' => $validData] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        self::assertNotEquals($validData, $data);

        ['sanitation_passed' => $status] = $this->sanitizer->sanitize($data, $rules);

        self::assertTrue($status);
    }

    /** @test */
    public function it_returns_array_of_errors_for_every_invalid_value_if_data_can_not_pass_sanitation()
    {
        ['data' => $data, 'rules' => $rules] = (new Json())(file_get_contents(__DIR__ . '/fixtures/data_causes_errors.json'));
        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize($data, $rules);

        self::assertFalse($status);
        self::assertSame(array_keys($errors), array_keys($data));
    }

    /** @test */
    public function it_returns_empty_array_if_no_rules_was_provided()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        ['data' => $result] = $this->sanitizer->sanitize($data, []);

        self::assertEmpty($result);
    }

    /** @test */
    public function it_returns_error_when_no_data_was_found_by_specified_rule_key()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        ['data' => $errors, 'sanitation_passed' => $status] = $this->sanitizer->sanitize($data, ['nonexistent_key' => ['data_type' => ['string']]]);

        self::assertFalse($status);
        self::assertArrayHasKey('nonexistent_key', $errors);
    }

    /** @test */
    public function it_returns_global_error_if_invalid_data_format_was_provided()
    {
        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize('data', [], 'wrong_data_format');

        self::assertFalse($status);
        self::assertArrayHasKey('global', $errors);
    }

    /** @test */
    public function it_returns_global_error_when_unable_to_parse_specified_data_format()
    {
        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize('not_a_json_data', [], 'json');

        self::assertFalse($status);
        self::assertArrayHasKey('global', $errors);
    }

    /** @test */
    public function it_returns_global_error_when_invalid_rules_was_provided()
    {
        ['data' => $data] = (new Json())(file_get_contents(__DIR__ . '/fixtures/valid_data.json'));

        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize($data, ['integer' => []], 'json');

        self::assertFalse($status);
        self::assertArrayHasKey('global', $errors);
    }

    /** @test */
    public function it_may_be_extended_with_custom_data_types()
    {
        $dataType = new class implements Validatable {
            public static $slug = 'test_data_type';

            public function validate($data, array $options = []): bool
            {
                return true;
            }

            public function getValidationErrorMessage(): string
            {
                return 'Something went wrong';
            }
        };
        $sanitizer = new Sanitizer([$dataType]);

        self::assertContains($dataType::$slug, $sanitizer->getAvailableDataTypes());
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

    /** @test */
    public function it_generates_an_error_for_wrong_data_passed_in_integer_field_data_type()
    {
        $field = 'some_integer_field';
        $fieldValue = '123test';
        $data = [$field => $fieldValue];
        $rules = [$field => ['data_type' => Integer::$slug]];

        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize($data, $rules);

        self::assertFalse($status);
        self::assertArrayHasKey('message', $errors[$field]);
        self::assertEquals($errors[$field]['data'], $fieldValue);
    }

    /** @test */
    public function it_generates_an_error_for_wrong_data_passed_in_russian_federal_phone_number_field_data_type()
    {
        $field = 'some_russian_federal_phone_number_field';
        $fieldValue = '0500';
        $data = [$field => $fieldValue];
        $rules = [$field => ['data_type' => RussianFederalPhoneNumber::$slug]];

        ['sanitation_passed' => $status, 'data' => $errors] = $this->sanitizer->sanitize($data, $rules);

        self::assertFalse($status);
        self::assertArrayHasKey('message', $errors[$field]);
        self::assertEquals($errors[$field]['data'], $fieldValue);
    }
}
