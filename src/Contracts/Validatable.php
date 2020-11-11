<?php

namespace Influx\Sanitizer\Contracts;

interface Validatable
{
    /**
     * Check if data passes validation.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function validate($data, array $options = []): bool;


    /**
     * Return validation error message for the external usage.
     *
     * @return string
     */
    public function getValidationErrorMessage(): string;
}