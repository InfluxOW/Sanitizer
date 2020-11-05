<?php

namespace Influx\Sanitizer\Contracts;

interface DataType
{
    /**
     * Checks if value passes validation.
     *
     * @return bool
     */
    public function validate(): bool;


    /**
     * Returns error message.
     *
     * @return string
     */
    public function getErrorMessage(): string;
}