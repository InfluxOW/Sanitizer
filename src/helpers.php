<?php

function array_unset_keys(array $array, array $keys)
{
    return array_diff_key($array, array_combine($keys, $keys));
}

function getReadableName(string $string)
{
    if (strpos($string, '_')) {
        return str_replace('_', ' ', $string);
    }

    if (strpos($string, '\\')) {
        $path = explode('\\', $string);
        $string = array_pop($path);
    }

    return trim(implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $string))));
}