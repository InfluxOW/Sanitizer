<?php

function class_uses_trait(string $class, string $trait): bool
{
    return array_key_exists($class, (new \ReflectionClass($trait))->getTraits());
}