<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;
use Influx\Sanitizer\Traits\HasDefaultNormalizationErrorMessage;
use Influx\Sanitizer\Traits\HasDefaultValidationErrorMessage;

class RussianFederalPhoneNumber implements Validatable, Normalizable
{
    use HasDefaultNormalizationErrorMessage;
    use HasDefaultValidationErrorMessage;

    public static $slug = 'russian_federal_phone_number';

    public function validate($data, array $options = []): bool
    {
        return preg_match('/^7[489]\d{9}$/', $data);
    }

    public function normalize($data, array $options = [])
    {
        if (preg_match('/^(\+7|7|8)?[\s\-]?\(?[489]\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/', $data)) {
            $phoneNumber = preg_replace('/\D/', '', $data);

            return preg_replace('/^8/', '7', $phoneNumber);
        }

        throw new NormalizationException($this->getNormalizationErrorMessage());
    }
}