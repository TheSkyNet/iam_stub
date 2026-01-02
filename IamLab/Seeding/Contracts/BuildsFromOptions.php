<?php
declare(strict_types=1);

namespace IamLab\Seeding\Contracts;

/**
 * Implement to allow constructing a seeder from parsed CLI options.
 */
interface BuildsFromOptions
{
    /** @param array<string,mixed> $options */
    public static function fromOptions(array $options): self;
}
