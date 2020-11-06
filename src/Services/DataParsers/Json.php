<?php

namespace Influx\Sanitizer\Services\DataParsers;

class Json
{
    public function __invoke(string $data): array
    {
        return json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    }
}