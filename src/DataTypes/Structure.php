<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;

class Structure implements DataType
{
    public $data;
    protected array $keys;

    public function __construct($data, array $keys)
    {
        $this->data = $data;
        $this->keys = $keys;
    }

    public function validate(): bool
    {
        foreach ($this->keys as $key => $value) {
            if (is_array($value) && array_key_exists($key, $this->data) && is_array($this->data[$key])) {
                $nestedStructure = new self($this->data[$key], $value);

                if ($nestedStructure->validate()) {
                    continue;
                }

                return false;
            }

            if (array_key_exists($value, $this->data)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function getErrorMessage(): string
    {
        return "Provided data doesn't match with the specified structure.";
    }
}