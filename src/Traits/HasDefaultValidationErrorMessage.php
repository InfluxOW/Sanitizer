<?php

namespace Influx\Sanitizer\Traits;

trait HasDefaultValidationErrorMessage
{
    public function getValidationErrorMessage(): string
    {
        $readableDataTypeName = ucfirst(getReadableName(static::$slug ?? get_class($this)));

        return "{$readableDataTypeName} is not the type of provided data.";
    }
}