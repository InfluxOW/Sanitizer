<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;

class Structure extends DataType
{
    public static $slug = 'structure';

    public function validate($data, array $options = []): bool
    {
        $this->validateInput($data, $options);

        foreach ($options['structure'] as $key => $value) {
            if ($key === '*' && is_array($value)) {
                foreach ($data as $datum) {
                    if (is_array($datum) && (new self())->validate($datum, ['structure' => $value])) {
                        continue;
                    }

                    return false;
                }

                continue;
            }

            if (is_array($value) && array_key_exists($key, $data) && is_array($data[$key])) {
                if ((new self())->validate($data[$key], ['structure' => $value])) {
                    continue;
                }

                return false;
            }

            if (array_key_exists($value, $data)) {
                continue;
            }

            return false;
        }

        return true;
    }

    public function getValidationErrorMessage(): string
    {
        return "Provided data doesn't match with the specified structure.";
    }

    private function validateInput($data, array $options): void
    {
        if (! is_array($data)) {
            throw new \InvalidArgumentException("Unable to handle non array structures.");
        }


        if (! array_key_exists('structure', $options)) {
            throw new \InvalidArgumentException("Please, put structure data under the 'structure' key.");
        }
    }
}
