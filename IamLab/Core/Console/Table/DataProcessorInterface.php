<?php

namespace IamLab\Core\Console\Table;

interface DataProcessorInterface
{
    /**
     * Process raw data into normalized table format
     *
     * @return array ['headers' => array, 'rows' => array]
     */
    public function process(array $data): array;

    /**
     * Check if the processor can handle the given data structure
     */
    public function canProcess(array $data): bool;
}
