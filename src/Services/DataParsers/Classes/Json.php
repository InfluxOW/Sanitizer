<?php

namespace Influx\Sanitizer\Services\DataParsers\Classes;

use Influx\Sanitizer\Services\DataParsers\Contracts\Invokable;

class Json implements Invokable
{
    public function __invoke($data): array
    {
        try {
            return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Exception | \Error $e) {
            throw new \InvalidArgumentException("Unable to handle provided data");
        }
    }
}
