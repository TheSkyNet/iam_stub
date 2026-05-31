<?php

namespace IamLab\Core\Console\Table;

interface RendererInterface
{
    /**
     * Render the table content to output
     */
    public function render(string $content): void;

    /**
     * Check if the renderer supports the current environment
     */
    public function isSupported(): bool;
}
