<?php

namespace Influx\Sanitizer\DataTypes;

use Influx\Sanitizer\Contracts\DataType;
use Influx\Sanitizer\Contracts\Normalizable;
use Influx\Sanitizer\Exceptions\NormalizationException;

class RussianFederalPhoneNumber implements DataType, Normalizable
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function validate(): bool
    {
        return preg_match('/^(\+7|7|8)?[\s\-]?\(?[489]\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/', $this->data);
    }

    public function normalize(): DataType
    {
        if (preg_match('/^(\+7|7|8)?[\s\-]?\(?[489]\d{2}\)?[\s\-]?\d{3}[\s\-]?\d{2}[\s\-]?\d{2}$/', $this->data)) {
            $phoneNumber = preg_replace('/\D/', '', $this->data);

            return new self(
                preg_replace('/^8/', '7', $phoneNumber)
            );
        }

        throw new NormalizationException($this->getErrorMessage());
    }

    public function getErrorMessage(): string
    {
        return "Provided data is not a russian federal phone number and couldn't be converted to it.";
    }
}