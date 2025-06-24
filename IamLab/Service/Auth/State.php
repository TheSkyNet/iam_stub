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
    public function __construct(array $arguments = array())
    {
        if (!empty($arguments))
        {
            foreach ($arguments as $property => $argument) {
                if ($argument instanceOf Closure)
                {
                    $this->{$property} = $argument;
                }
                else
                {
                    $this->{$property} = $argument;
                }
            }
        }
    }
}
