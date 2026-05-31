<?php

namespace IamLab\Core\Console\Table\Renderers;

use IamLab\Core\Console\Table\RendererInterface;

class ConsoleRenderer implements RendererInterface
{
    /**
     * Render the table content to output
     */
    #[\Override]
    public function render(string $content): void
    {
        echo $content;
    }

    /**
     * Check if the renderer supports the current environment
     */
    #[\Override]
    public function isSupported(): bool
    {
        // Console renderer is always supported in CLI environment
        return PHP_SAPI === 'cli' || defined('STDIN');
    }
}
