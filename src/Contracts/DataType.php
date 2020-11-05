<?php

namespace Influx\Sanitizer\Contracts;

interface DataType
{
    /**
     * Checks if data passes validation.
     *
     * @param $data
     * @param array $options
     * @return bool
     */
    public function validate($data, array $options = []): bool;


    /**
     * Returns error message.
     *
     * @return string
     */
    public function getErrorMessage(): string;
}