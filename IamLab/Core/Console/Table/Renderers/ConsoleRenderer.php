<?php

namespace IamLab\Core\Console\Table\Renderers;

use IamLab\Core\Console\Table\RendererInterface;

class ConsoleRenderer implements RendererInterface
{
    /**
     * Render the table content to output
     *
     * @param string $content
     * @return void
     */
    public function render(string $content): void
    {
        echo $content;
    }

    /**
     * Check if the renderer supports the current environment
     *
     * @return bool
     */
    public function isSupported(): bool
    {
        // Console renderer is always supported in CLI environment
        return php_sapi_name() === 'cli' || defined('STDIN');
    }
}