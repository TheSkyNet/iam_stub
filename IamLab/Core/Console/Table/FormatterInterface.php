<?php

namespace IamLab\Core\Console\Table;

interface FormatterInterface
{
    /**
     * Calculate column widths based on headers and data
     *
     * @param array $headers
     * @param array $rows
     * @return array
     */
    public function calculateWidths(array $headers, array $rows): array;

    /**
     * Format a table row with proper padding and alignment
     *
     * @param array $row
     * @param array $headers
     * @param array $widths
     * @return string
     */
    public function formatRow(array $row, array $headers, array $widths): string;

    /**
     * Format table borders (top, middle, bottom)
     *
     * @param array $widths
     * @param string $type ('top', 'middle', 'bottom')
     * @return string
     */
    public function formatBorder(array $widths, string $type = 'middle'): string;

    /**
     * Format table title with proper centering
     *
     * @param string $title
     * @param int $totalWidth
     * @return string
     */
    public function formatTitle(string $title, int $totalWidth): string;
}