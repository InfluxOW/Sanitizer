<?php

namespace Influx\Sanitizer\Contracts;

interface Normalizable
{
    /**
     * Returns normalized data that passes validation.
     *
     * @param $data
     * @param array $options
     * @return mixed
     * @throws \Influx\Sanitizer\Exceptions\NormalizationException
     */
    public function normalize($data, array $options = []);

    /**
     * Returns normalization error message.
     *
     * @return string
     */
    public function getNormalizationErrorMessage(): string;
}