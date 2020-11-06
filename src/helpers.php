<?php

function array_unset_keys(array $array, array $keys)
{
    return array_diff_key($array, array_combine($keys, $keys));
}