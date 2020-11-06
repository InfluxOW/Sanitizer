<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;

class Structure implements Validatable
{
    public static $slug = 'structure';

    public function validate($data, array $options = []): bool
    {
        $this->validateData($data);
        $this->validateOptions($options);

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

    private function validateOptions(array $options): void
    {
        if (array_key_exists('structure', $options)) {
            return;
        }

        throw new \InvalidArgumentException("Please, put structure data under the 'structure' key.");
    }

    private function validateData($data): void
    {
        if (is_array($data)) {
            return;
        }

        throw new \InvalidArgumentException("Unable to handle non array structures.");
    }
}