<?php

namespace IamLab\Core\API;

use JsonSerializable;
use Phalcon\Mvc\Model;
use function App\Core\Helpers\cast;
use function App\Core\Helpers\merge_objects;

abstract class Entity extends Model implements JsonSerializable
{
    protected $casts = [];
    protected $amends = [];


    protected function cast()
    {
        $ca = [];
        foreach ($this->casts as $key => $value) {
            $name = ucfirst(is_int($key) ? $value : $key);
            $index = is_int($key) ? $value : $key;

            if (is_callable($this, "cast$name")) {
                $ca[$index] = $this->{"cast$name"}();
            } else if (method_exists($this, "cast$name")) {
                $ca[$index] = $this->{"cast$name"}();
            } else if (property_exists($this, $index)) {
                $ca[$index] = cast($key, gettype($value));
            }

        }
        return (object)$ca;
    }

    protected function amend()
    {
        $ca = [];
        foreach ($this->amends as $key => $value) {
            $name = ucfirst($key);
            if (is_callable($this, "amend$name")) {
                $ca[$key] = $this->{"amend$name"}();
            } else if (method_exists($this, "cast$name")) {
                $ca[$key] = $this->{"cast$name"}();
            }
        }
        return (object)$ca;
    }

    function jsonSerialize(): array
    {
        return (array)merge_objects(
            (object)$this->toArray(),
            $this->cast(),
            $this->amend()
        );
    }


}


