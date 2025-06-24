<?php

namespace IamLab\Core\Console\Table\Processors;

use IamLab\Core\Console\Table\DataProcessorInterface;

class MultiDimensionalArrayProcessor implements DataProcessorInterface
{
    /**
     * Process multi-dimensional array into normalized table format
     *
     * @param array $data
     * @return array
     */
    public function process(array $data): array
    {
        if (empty($data)) {
            return ['headers' => [], 'rows' => []];
        }

        $rows = [];
        $headers = [];

        // Multi-dimensional array or array of objects
        foreach ($data as $index => $row) {
            if (is_object($row)) {
                $row = (array) $row;
            }

            // Collect all possible headers
            $headers = array_unique(array_merge($headers, array_keys($row)));
            $rows[$index] = $row;
        }

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    /**
     * Check if this processor can handle the given data structure
     *
     * @param array $data
     * @return bool
     */
    public function canProcess(array $data): bool
    {
        if (empty($data)) {
            return true;
        }

        // Check if it's a multi-dimensional array or array of objects
        $firstValue = reset($data);
        return is_array($firstValue) || is_object($firstValue);
    }
}