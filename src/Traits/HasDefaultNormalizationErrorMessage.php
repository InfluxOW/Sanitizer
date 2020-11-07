<?php

namespace Influx\Sanitizer\Traits;

trait HasDefaultNormalizationErrorMessage
{
    public function getNormalizationErrorMessage(): string
    {
        $readableDataTypeName = ucfirst(getReadableName(static::$slug ?? get_class($this)));

        return "{$readableDataTypeName} is not the type that provided data could be converted to.";
    }
}
