# Sanitizer
[![codecov](https://codecov.io/gh/InfluxOW/sanitizer/branch/main/graph/badge.svg?token=eUXetBBAL5)](https://codecov.io/gh/InfluxOW/sanitizer)
![Main workflow](https://github.com/InfluxOW/Sanitizer/workflows/Main%20workflow/badge.svg)

## Description
A simple app that allows you to sanitize input with some specified data types.

It gives you 6 default data types out of the box:
- String
- Integer
- Float
- One Type Elements Array
- Russian Federal Phone Number
- Structure

It also supports 2 default input types out of the box:
- Array
- Json

You can extend these lists by adding custom data types and parsers.

## Installation
`composer require influx/sanitizer`

## Usage

> ##### 1. Instantiating sanitizer
    
`$sanitizer = new Sanitizer($customDataTypes = [CustomDataType::class], $customParsers = [CustomParser::class])`

*Notices*: 
- **Custom data types must implement `Influx\Sanitizer\DataTypes\Contracts\Validatable` interface**
- If your data type could be prepared for validation it should implement `Influx\Sanitizer\DataTypes\Contracts\PreparesForValidation` interface
- If your data type could be prepared for transmission after it has been validated it should implement `Influx\Sanitizer\DataTypes\Contracts\PreparesForTransmission` interface
- **Custom parsers must implement `Influx\Sanitizer\Services\DataParsers\Contracts\Invokable` interface**
    
> ##### 2. Preparing rules
    
You can pass any data and options to the rules array.

*Notices*: 
- Every rule requires `sanitizer_data_type` field where you should put type of data there should be
- Other fields passed to the rule will come to the exact data type in the `array $options` variable

**Example**
```
$rules = [
    'some_integer_field' => ['sanitizer_data_type' => 'integer'],
    'some_string_field' => ['sanitizer_data_type] => 'string'],
    'some_structure_field' => ['sanitizer_data_type' => 'structure', ['structure' = ['*' => ['key']]],
    'some_one_type_elements_array_field' => ['sanitizer_data_type => 'one_type_elements_array', 'elements_type' => 'integer'],
]
```

> ##### 3. Preparing data
    
`$data = ['some_integer_field' => '123test']`

or

```
$data = '{
  "some_integer_field": "123test",
  "some_string_field": "123test",
  "some_float_field": "123456.45",
  "some_russian_federal_phone_number_field": "0500"
}'
```

> ##### 4. Sanitizing
    
`$result = $sanitizer->sanitize($data, $dataFormat, $rules)`\

**Notices:**
- You can also pass data as `$value` => `$rule` in the first argument without specifying field names.
- If you are passing an array as the first argument it won't be parsed and will be processed as is, otherwise it will be parsed using specified data format parser.

`$result`:
- If something 'globally' went wrong (e.g. invalid rules has been provided, data couldn't be parsed) it will consist of error within 'global_errors' field.
- If any validation failed it will consist of array of errors within 'sanitation_errors' field.
- If data passed sanitation it will consist of array of sanitized data withing 'sanitized_data' field.

## Data Types Information
Different data types requires different information. \
Every data type needs at least value to handle. It will come from the input. \
But some data types requires options. They will come from the rule data.
- One Type Elements Array requires `elements_type` option

Which means you should interact with it like this:
```
$rules = [
    'some_one_type_elements_array_field' => ['data_type => 'one_type_elements_array', 'elements_type' => 'integer'],
]
```

- Structure requires 'structure' option

Which means you should interact with it like this:
```
$rules = [
    'some_structure_field' => ['data_type' => 'structure', ['structure' = ['*' => ['key']]],
]
```

Other data types needs nothing but the `sanitizer_data_type` field as was mentioned before.