<?php

namespace IamLab\Core\Command;

/**
 * Simple progress bar implementation
 */
class ProgressBar
{
    private int $current = 0;

    private int $width = 50;

    public function __construct(private readonly int $total)
    {
    }

    public function advance(int $step = 1): void
    {
        $this->current += $step;
        $this->display();
    }

    public function finish(): void
    {
        $this->current = $this->total;
        $this->display();
        echo "\n";
    }

    private function display(): void
    {
        $percent = $this->total > 0 ? ($this->current / $this->total) * 100 : 100;
        $filled = (int)(($this->current / $this->total) * $this->width);
        $empty = $this->width - $filled;

        $bar = str_repeat('=', $filled) . str_repeat('-', $empty);
        printf("\r[%s] %d%% (%d/%d)", $bar, $percent, $this->current, $this->total);
    }
}
