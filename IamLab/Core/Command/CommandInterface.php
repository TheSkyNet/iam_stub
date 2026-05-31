<?php

namespace IamLab\Core\Command;

interface CommandInterface
{
    /**
     * Execute the command
     *
     * @param array $arguments Command arguments
     * @param array $options Command options/flags
     * @return int Exit code (0 for success, non-zero for failure)
     */
    public function execute(array $arguments = [], array $options = []): int;

    /**
     * Get command signature/usage
     */
    public function getSignature(): string;

    /**
     * Get command description
     */
    public function getDescription(): string;

    /**
     * Get command help text
     */
    public function getHelp(): string;
}
