<?php

namespace IamLab\Core\Console\Table;

interface RendererInterface
{
    /**
     * Render the table content to output
     *
     * @param string $content
     * @return void
     */
    public function render(string $content): void;

    /**
     * Check if the renderer supports the current environment
     *
     * @return bool
     */
    public function isSupported(): bool;
}