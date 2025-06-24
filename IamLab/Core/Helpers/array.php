<?php

namespace App\Core\Helpers;


use IamLab\Core\Collection\Collection;
use stdClass;

function cast($value, $cast)
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

function splat(...$args): array
{
    return $args;
}


function concatenate($transform, ...$strings) {
    $string = implode('', $strings);
    return($transform($string));
}


function collect($collection): Collection
{
    return new Collection($collection);
}