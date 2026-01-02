<?php
declare(strict_types=1);

/**
 * Seeder registry mapping.
 *
 * Map short names (CLI-friendly) to fully-qualified class names.
 * Add or remove seeders here; the CLI picks them up automatically.
 */
return [
    // Core seeders
    'RolesSeeder' => \IamLab\Migrations\Seeders\RolesSeeder::class,
    'SiteSettingsSeeder' => \IamLab\Migrations\Seeders\SiteSettingsSeeder::class,

    // Example seeder with CLI options; safe no-op by default
    'ExampleUserSeeder' => \IamLab\Seeders\ExampleUserSeeder::class,
];
