<?php

namespace IamLab\Core\Console\Table;

interface TableInterface
{
    /**
     * Set the data to be displayed in the table
     */
    public function setData(array $data): self;

    /**
     * Set the table title
     */
    public function setTitle(?string $title): self;

    /**
     * Render and output the table
     */
    public function render(): void;

    /**
     * Get the table as a string without outputting
     */
    public function toString(): string;
}
