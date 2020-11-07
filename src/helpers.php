<?php

use Influx\Sanitizer\Contracts\Validatable;
use Influx\Sanitizer\DataTypes\OneTypeElementsArray;

/**
 * Returns array without specified keys.
 *
 * @param array $array
 * @param array $keys
 * @return array
 */
function array_unset_keys(array $array, array $keys): array
{
    return array_diff_key($array, array_combine($keys, $keys));
}

/**
 * Transforms string into more readable way.
 *
 * @param string $string
 * @return string
 */
function getReadableName(string $string): string
{
    if (strpos($string, '_')) {
        return str_replace('_', ' ', $string);
    }

    if (strpos($string, '\\')) {
        $string = class_basename($string);
    }

    return trim(implode(' ', array_map('strtolower', preg_split('/(?=[A-Z])/', $string))));
}

/**
 * Returns class basename.
 *
 * @param string $class
 * @return string
 */
function class_basename(string $class): string
{
    $path = explode('\\', $class);

    return array_pop($path);
}

/**
 * Returns path basename.
 *
 * @param string $path
 * @return string
 */
function path_basename(string $path): string
{
    $pathParts = explode('/', $path);
    $fileNameWithExtension = array_pop($pathParts);
    [$fileNameWithoutExtension] = explode('.', $fileNameWithExtension);

    return $fileNameWithoutExtension;
}

/**
 * Returns slugified string.
 *
 * @param string $string
 * @return string
 */
function slugify(string $string): string
{
    return str_replace(' ', '_', getReadableName($string));
}

/**
 * Parses specified directory classes and returns them with their slugs.
 * Example:
 * [
 *  'class_one' => ClassOne::class,
 *  'class_two' =? ClassTwo::class,
 * ]
 *
 * @param string $directory
 * @param string $namespace
 * @return array
 */
function parse_directory_classes_to_slug_classname_way(string $directory, string $namespace): array
{
    $classes = [];
    $filePaths = glob("{$directory}/*.php");

    foreach ($filePaths as $filePath) {
        $classBasename = path_basename($filePath);
        $class = "{$namespace}{$classBasename}";
        $classes[] = $class;
    }

    return parse_classes_to_slug_classname_way($classes);
}

/**
 * Returns provided classes with their slugs.
 * Example:
 * [
 *  'class_one' => ClassOne::class,
 *  'class_two' =? ClassTwo::class,
 * ]
 *
 * @param array $classes
 * @return array
 */
function parse_classes_to_slug_classname_way(array $classes): array
{
    $result = [];

    foreach ($classes as $key => $class) {
        if (is_int($key)) {
            $key = $class::$slug ?? slugify($class);
        }

        $result[$key] = $class;
    }

    return $result;
}

/**
 * Check if every array element implements specified interface.
 *
 * @param array $array
 * @param string $interface
 * @return bool
 */
function check_array_elements_implements_interface(array $array, string $interface): bool
{
    foreach ($array as $class) {
        if (in_array($interface, class_implements($class), true)) {
            continue;
        }

        return false;
    }

    return true;
}

/**
 * Check if class constructor needs specified class instance.
 *
 * @param string $class
 * @param string $needed
 * @return bool
 * @throws \ReflectionException
 */
function check_class_constructor_needs_class(string $class, string $needed)
{
    $constructor = (new \ReflectionClass($class))->getConstructor();

    if  ($constructor) {
        foreach ($constructor->getParameters() as $reflectionParameter) {
            if ($reflectionParameter->getClass()->getName() === $needed) {
                return true;
            }
        }
    }

    return false;
}