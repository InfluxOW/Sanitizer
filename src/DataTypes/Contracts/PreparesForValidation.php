<?php

namespace Influx\Sanitizer\DataTypes\Contracts;

interface PreparesForValidation
{
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