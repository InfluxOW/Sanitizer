<?php

namespace Influx\Sanitizer\Services\DataParsers\Contracts;

interface Invokable
{
    /**
     * Transforms provided data to array.
     *
     * @param $data
     * @throws \InvalidArgumentException
     * @return array
     */
    public function __invoke($data): array;
}
