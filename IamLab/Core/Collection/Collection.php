<?php

namespace IamLab\Core\Collection;

/**
 * Class Collection
 *
 * A custom collection class that extends Phalcon's collection and adds
 * common methods like each, map, and filter for easier data manipulation.
 * @package IamLab\Core\Collection
 */
class Collection extends \Phalcon\Support\Collection
{

    /**
     * Iterates over the items in the collection and applies a callback to each one.
     *
     * @param callable $callback The callback function to execute for each item.
     * @return static The collection instance for method chaining.
     */
    public function each(callable $callback): static
    {
        foreach ($this->data as $key => $item) {
            $callback($item);
        }
        return $this;
    }

    /**
     * Applies a callback to all items in the collection and replaces them with the result.
     *
     * @param callable $callback The callback function to apply to each item.
     * @return static The modified collection instance.
     */
    public function map(callable $callback): static
    {
        foreach ($this->data as $key => $item) {
            $this->data[$key] = $callback($item);
        }
        return $this;
    }

    /**
     * Filters the collection in-place using a callback function.
     *
     * Note: For each item, if the callback returns a truthy value, the item is
     * replaced by the result of executing the callback a second time.
     *
     * @param callable $callback The callback to apply.
     * @return static The modified collection instance.
     */
    public function filter(callable $callback): static
    {
        foreach ($this->data as $key => $item) {
            if ($callback($item)) {
                $this->data[$key] = $callback($item);
            }

        }
        return $this;
    }

    /**
     * Returns the first item in the collection.
     *
     * Note: This will raise a warning if the collection is empty.
     *
     * @return mixed The first item.
     */
    public function first(): mixed
    {
        return $this->data[0];
    }

}