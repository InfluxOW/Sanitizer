<?php

namespace Influx\Sanitizer\Contracts;

interface HasAfterValidationHook
{
    /**
     * Do something with data if it is valid.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function afterValidation($data, array $options = []);
}