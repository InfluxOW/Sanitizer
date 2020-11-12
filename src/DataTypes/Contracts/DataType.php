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
     * Prepare data for transmission outside if it was valid.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function prepareForTransmission($data, array $options = []);

    /**
     * Prepare data for validation.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function prepareForValidation($data, array $options = []);
}