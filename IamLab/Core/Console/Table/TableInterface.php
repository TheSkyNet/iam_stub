<?php

namespace IamLab\Core\Console\Table;

interface TableInterface
{
    /**
     * Set the data to be displayed in the table
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self;

    /**
     * Set the table title
     *
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): self;

    /**
     * Render and output the table
     *
     * @return void
     */
    public function render(): void;

    /**
     * Get the table as a string without outputting
     *
     * @return string
     */
    public function toString(): string;
}