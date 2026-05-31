<?php

/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 22/10/2015
 * Time: 19:30
 */

namespace IamLab\Service\Auth;

class State
{
    public function __construct(array $arguments = [])
    {
        foreach ($arguments as $property => $argument) {
            $this->{$property} = $argument instanceof Closure ? $argument : $argument;
        }
    }
}
