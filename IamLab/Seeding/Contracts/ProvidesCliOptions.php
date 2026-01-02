<?php
declare(strict_types=1);

namespace IamLab\Seeding\Contracts;

/**
 * Implement to contribute seeder-specific CLI options.
 * Use PHP getopt() format: trailing ':' means the option expects a value.
 */
interface ProvidesCliOptions
{
    /** @return array<int,string> */
    public static function cliOptions(): array;
}
