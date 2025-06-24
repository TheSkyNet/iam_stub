<?php

namespace IamLab\Core\Console\Table\Formatters;

use IamLab\Core\Console\Table\FormatterInterface;

class ConsoleTableFormatter implements FormatterInterface
{
    private int $minColumnWidth = 3;
    private int $padding = 2;

    /**
     * Calculate column widths based on headers and data
     *
     * @param array $headers
     * @param array $rows
     * @return array
     */
    public function calculateWidths(array $headers, array $rows): array
    {
        $widths = [];

        // Initialize with header lengths
        foreach ($headers as $header) {
            $widths[$header] = max(strlen($header), $this->minColumnWidth);
        }

        // Calculate widths based on data
        foreach ($rows as $row) {
            foreach ($headers as $header) {
                $value = isset($row[$header]) ? (string) $row[$header] : '';
                $widths[$header] = max($widths[$header], strlen($value));
            }
        }

        // Add padding
        foreach ($widths as $header => $width) {
            $widths[$header] = $width + $this->padding;
        }

        return $widths;
    }

    /**
     * Format a table row with proper padding and alignment
     *
     * @param array $row
     * @param array $headers
     * @param array $widths
     * @return string
     */
    public function formatRow(array $row, array $headers, array $widths): string
    {
        $output = '|';
        foreach ($headers as $header) {
            $value = isset($row[$header]) ? (string) $row[$header] : '';
            $output .= ' ' . str_pad($value, $widths[$header] - 1) . '|';
        }
        return $output;
    }

    /**
     * Format table borders (top, middle, bottom)
     *
     * @param array $widths
     * @param string $type
     * @return string
     */
    public function formatBorder(array $widths, string $type = 'middle'): string
    {
        $output = '+';
        foreach ($widths as $width) {
            $output .= str_repeat('-', $width) . '+';
        }
        return $output;
    }

    /**
     * Format table title with proper centering
     *
     * @param string $title
     * @param int $totalWidth
     * @return string
     */
    public function formatTitle(string $title, int $totalWidth): string
    {
        $titleBorder = str_repeat('=', $totalWidth);
        $titleRow = "| " . str_pad($title, $totalWidth - 4, ' ', STR_PAD_BOTH) . " |";
        
        return $titleBorder . "\n" . $titleRow . "\n" . $titleBorder;
    }

    /**
     * Calculate total table width
     *
     * @param array $widths
     * @return int
     */
    public function calculateTotalWidth(array $widths): int
    {
        return array_sum($widths) + count($widths) + 1;
    }

    /**
     * Set minimum column width
     *
     * @param int $width
     * @return self
     */
    public function setMinColumnWidth(int $width): self
    {
        $this->minColumnWidth = $width;
        return $this;
    }

    /**
     * Set column padding
     *
     * @param int $padding
     * @return self
     */
    public function setPadding(int $padding): self
    {
        $this->padding = $padding;
        return $this;
    }
}