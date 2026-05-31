<?php

namespace IamLab\Core\Console\Table;

interface FormatterInterface
{
    /**
     * Calculate column widths based on headers and data
     */
    public function calculateWidths(array $headers, array $rows): array;

    /**
     * Format a table row with proper padding and alignment
     */
    public function formatRow(array $row, array $headers, array $widths): string;

    /**
     * Format table borders (top, middle, bottom)
     *
     * @param string $type ('top', 'middle', 'bottom')
     */
    public function formatBorder(array $widths, string $type = 'middle'): string;

    /**
     * Format table title with proper centering
     */
    public function formatTitle(string $title, int $totalWidth): string;
}
