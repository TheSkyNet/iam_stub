<?php

namespace IamLab\Core\Console\Table;

interface DataProcessorInterface
{
    /**
     * Process raw data into normalized table format
     *
     * @param array $data
     * @return array ['headers' => array, 'rows' => array]
     */
    public function process(array $data): array;

    /**
     * Check if the processor can handle the given data structure
     *
     * @param array $data
     * @return bool
     */
    public function canProcess(array $data): bool;
}