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
        $string = class_basename($string);
    }

    return trim(implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $string))));
}

function class_basename(string $class)
{
    $path = explode('\\', $class);

    return array_pop($path);
}

function path_basename(string $path)
{
    $pathParts = explode('/', $path);
    $fileNameWithExtension = array_pop($pathParts);
    [$fileNameWithoutExtension] = explode('.', $fileNameWithExtension);

    return $fileNameWithoutExtension;
}

function slugify(string $string)
{
    return str_replace(' ', '_', getReadableName($string));
}

function parse_directory_classes_in_slug_classname_manner(string $directory, string $namespace)
{
    $result = [];
    $filePaths = glob("{$directory}/*.php");

    foreach ($filePaths as $filePath) {
        $classBasename = path_basename($filePath);
        $class = "{$namespace}{$classBasename}";
        $slug = $class::$slug ?? slugify($classBasename);
        $result[$slug] = $class;
    }

    return $result;
}