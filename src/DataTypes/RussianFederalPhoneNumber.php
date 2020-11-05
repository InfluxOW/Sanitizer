<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class RussianFederalPhoneNumber implements DataType, Normalizable
{
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

        throw new NormalizationException($this->getErrorMessage());
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a russian federal phone number and couldn't be converted to it.";
    }
}