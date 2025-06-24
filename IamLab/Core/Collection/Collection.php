<?php

namespace IamLab\Core\Collection;

class Collection extends \Phalcon\Support\Collection
{

    public function each($callback)
    {
        foreach ($this->data as $key => $item) {
            $callback($item);
        }
        return $this;
    }

    public function map($callback)
    {

        foreach ($this->data as $key => $item) {
            $this->data[$key] = $callback($item);
        }
        return $this;
    }

    public function filter($callback)
    {
        foreach ($this->data as $key => $item) {
            if ($callback($item)) {
                $this->data[$key] = $callback($item);
            }

        }
        return $this;
    }

    public function first()
    {
        return $this->data[0];
    }

}