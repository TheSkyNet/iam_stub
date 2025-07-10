<?php

namespace App\Core\Helpers;


use IamLab\Core\Collection\Collection;
use stdClass;

/**
 * Cast a value to a specified type.
 *
 * @param mixed $value The value to cast.
 * @param string $cast The target type ('int', 'float', 'bool', 'string').
 * @return mixed The casted value, or the original value if the cast type is not supported.
 */
function cast(mixed $value, string $cast): mixed
{
    // cast a value in to a type
    return match ($cast) {
        'int' => (int)$value,
        'float' => (float)$value,
        'bool' => (bool)$value,
        'string' => (string)$value,
        default => $value,
    };

}

/**
 * Merge multiple objects into a new one.
 *
 * Properties from later objects will overwrite properties from earlier ones.
 * If the first object is an instance of stdClass, a new stdClass is created.
 * Otherwise, a new instance of the first object's class is created.
 *
 * @param object ...$objects A variable number of objects to merge.
 * @return object|void A new object with merged properties, or void if no objects are provided.
 */
function merge_objects(...$objects)
{

    if (count($objects) < 1) {
        return;
    }
    if ($objects[0] instanceof stdClass) {
        $new_object = new stdClass();
    } else {
        $class= get_class($objects[0]);
        $new_object = new $class;
    }

    foreach ($objects as $object) {
        foreach ($object as $property => $value) {
            $new_object->$property = $value;
        }

    }
    return $new_object;
}

/**
 * Collect all arguments passed to the function into a single array.
 *
 * This is a simple helper for variadic arguments.
 *
 * @param mixed ...$args A variable number of arguments.
 * @return array An array containing all the arguments.
 */
function splat(...$args): array
{
    return $args;
}


/**
 * Concatenate multiple strings and apply a transformation function to the result.
 *
 * @param callable $transform The function to apply to the concatenated string.
 * @param string ...$strings A variable number of strings to concatenate.
 * @return mixed The result of the transformation function.
 */
function concatenate(callable $transform, ...$strings): mixed
{
    $string = implode('', $strings);
    return($transform($string));
}


/**
 * Create a new Collection instance from an array or iterable.
 *
 * @param array $collection The items to include in the collection.
 * @return Collection A new Collection instance.
 */
function collect(array $collection): Collection
{
    return new Collection($collection);
}