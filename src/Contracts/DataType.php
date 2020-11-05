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
     * Returns itself with normalized value.
     *
     * @return \Influx\Sanitizer\Contracts\DataType
     * @throws \Influx\Sanitizer\Exceptions\NormalizationException
     */
    public function normalize(): DataType;

    /**
     * Returns error message.
     *
     * @return string
     */
    public function getErrorMessage(): string;

    /**
     * Returns provided value.
     *
     * @return mixed
     */
    public function getValue();
}