<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\HasBeforeValidationHook;
use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class RussianFederalPhoneNumber implements Validatable, HasBeforeValidationHook
{
    use HasDefaultValidationErrorMessage;

    public static $slug = 'russian_federal_phone_number';

    public function validate($data, array $options = []): bool
    {
        try {
            return preg_match('/^7[489]\d{9}$/', $data);
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException("Unable to handle non string values.");
        }
    }

    public function beforeValidation($data, array $options = [])
    {
        try {
            if (preg_match('/^(\+7|7|8)?[\s\-]?\(?[489]\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/', $data)) {
                $phoneNumber = preg_replace('/\D/', '', $data);

                return preg_replace('/^8/', '7', $phoneNumber);
            }
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException('Unable to handle provided data.');
        }

        throw new \InvalidArgumentException('Unable to handle provided data.');
    }
}
