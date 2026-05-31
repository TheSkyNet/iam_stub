<?php

namespace IamLab\Core\Console\Table\Processors;

use IamLab\Core\Console\Table\DataProcessorInterface;

class AssociativeArrayProcessor implements DataProcessorInterface
{
    /**
     * Process associative array into normalized table format
     */
    #[\Override]
    public function process(array $data): array
    {
        if ($data === []) {
            return ['headers' => [], 'rows' => []];
        }

        // Simple associative array - convert to single row table
        $headers = array_keys($data);
        $rows = [$data];

        return [
            'headers' => $headers,
            'rows' => $rows
        ];
    }

    /**
     * Check if this processor can handle the given data structure
     */
    #[\Override]
    public function canProcess(array $data): bool
    {
        if ($data === []) {
            return true;
        }

        // Check if it's a simple associative array (not multi-dimensional)
        $firstValue = reset($data);
        return !is_array($firstValue) && !is_object($firstValue);
    }
}
