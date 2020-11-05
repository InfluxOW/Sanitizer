<?php

namespace Influx\Sanitizer\Contracts;

interface DataType
{
    /**
     * Checks if value satisfies validation rule.
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

    /**
     * Returns provided data.
     *
     * @return mixed
     */
    public function getData();
}