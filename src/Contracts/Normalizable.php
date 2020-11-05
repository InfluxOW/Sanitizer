<?php

namespace Influx\Sanitizer\Contracts;

interface Normalizable
{
    /**
     * Returns DataType resolving instance with normalized value.
     *
     * @return \Influx\Sanitizer\Contracts\DataType
     * @throws \Influx\Sanitizer\Exceptions\NormalizationException
     */
    public function normalize(): DataType;
}