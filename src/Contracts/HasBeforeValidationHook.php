<?php

namespace Influx\Sanitizer\Contracts;

interface HasBeforeValidationHook
{
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