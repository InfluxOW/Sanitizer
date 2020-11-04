<?php

namespace Influx\Sanitizer\DataTypes;

interface DataType
{
    public function validate(): bool;

    public function normalize(): DataType;

    public function getErrorMessage(): string;
}