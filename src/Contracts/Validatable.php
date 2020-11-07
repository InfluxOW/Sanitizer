<?php

namespace Influx\Sanitizer\Contracts;

interface Validatable
{
    /**
     * Checks if data passes validation.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return bool
     */
    public function validate($data, array $options = []): bool;


    /**
     * Returns validation error message.
     *
     * @return string
     */
    public function getValidationErrorMessage(): string;
}
