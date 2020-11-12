<?php

namespace Influx\Sanitizer\DataTypes\Implementations;

use Influx\Sanitizer\DataTypes\DataType;

class RussianFederalPhoneNumber extends DataType
{
    public static $slug = 'russian_federal_phone_number';

    public function validate($data, array $options = []): bool
    {
        $this->verifyInput($data);

        return preg_match('/^7[489]\d{9}$/', $data);
    }

    public function beforeValidation($data, array $options = [])
    {
        $this->verifyInput($data);

        if (preg_match('/^(\+7|7|8)?[\s\-]?\(?[489]\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/', $data)) {
            $phoneNumber = preg_replace('/\D/', '', $data);

            return preg_replace('/^8/', '7', $phoneNumber);
        }

        throw new \InvalidArgumentException('Unable to convert provided type of data to russian federal phone number.');
    }

    private function verifyInput($data): void
    {
        try {
            (string) $data;
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException('Unable to handle non stringable data.');
        }
    }

}
