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

    1. Instantiating sanitizer
`$sanitizer = new Sanitizer($customDataTypes = [CustomDataType::class], $customParsers = [CustomParser::class])`

*Notices*: 
- **Custom data types must implement `Influx\Sanitizer\Contracts\Validatable` interface**
- If your data type could be normalized it should implement `Influx\Sanitizer\Contracts\Normalizable` interface
- **Custom parsers must implement `Influx\Sanitizer\Services\DataParsers\Contracts\Invokable` interface**


    2. Preparing rules.
You can pass any data and options to the rule.\
*Notices*: 
- Every rule requires `data_type` field where you should put type of data there should be
- Other fields passed to the rule will come to the exact data type in the `array $options` variable

**Example**
```
$rules = [
    'any_value' => ['data_type' => 'integer'],
    'other_value' => ['data_type] => 'string'],
    'structure' => ['data_type' => 'structure', ['structure' = ['*' => ['key']]],
    'one_type_elements_array' => ['data_type => 'one_type_elements_array', 'elements_type' => 'integer'],
]
```

    3. Preparing data
`$data = ['some_data']`

or

```
$data = '{
  "integer": "123test",
  "float": "123test",
  "string": [79502885623],
  "russian_federal_phone_number": "0500"
}'
```

    4. Sanitizing
`$result = $sanitizer->sanitize($data, $rules)`\
`$result` is array of two keys:
- 'sanitation_passed' => `true` (if everything went ok) or `false` (if sanitation failed)
- 'data' => contains an array of valid data (if previous field is `true`) or an array of errors (if previous field is `false`)

## Data Types Information
Different data types requires different information. \
Every data type needs at least value to handle. It will come from the input. \
But some data types requires options. They will come from the rule data.
- One Type Elements Array requires `elements_type` option

Which means you should interact with it like this:
```
$rules = [
    'one_type_elements_array' => ['data_type => 'one_type_elements_array', 'elements_type' => 'integer'],
]
```

- Structure requires 'structure' option

Which means you should interact with it like this:
```
$rules = [
    'structure' => ['data_type' => 'structure', ['structure' = ['*' => ['key']]],
]
```

Other data types needs nothing but the `data_type` field as was mentioned before.