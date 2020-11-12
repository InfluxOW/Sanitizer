<?php

namespace Influx\Sanitizer\DataTypes\Contracts;

interface PreparesForTransmission
{
    /**
     * Prepare data for transmission outside if it was valid.
     *
     * @param $data
     * @param array $options
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function prepareForTransmission($data, array $options = []);
}
