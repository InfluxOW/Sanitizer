<?php

function class_uses_trait(string $class, string $trait): bool
{
    return array_key_exists($trait, (new \ReflectionClass($class))->getTraits());
}