<?php

namespace Influx\Sanitizer\DataTypes\Contracts;

interface DataType
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

    /**
     * Do something with data if it is valid.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function afterValidation($data, array $options = []);

    /**
     * Do something with data before it has been validated.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function beforeValidation($data, array $options = []);
}