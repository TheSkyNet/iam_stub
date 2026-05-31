<?php

declare(strict_types=1);

use IamLab\Migrations\Seeders\RolesSeeder;
use IamLab\Migrations\Seeders\SiteSettingsSeeder;
use IamLab\Seeders\ExampleUserSeeder;

return [
    // Core seeders
    'RolesSeeder' => RolesSeeder::class,
    'SiteSettingsSeeder' => SiteSettingsSeeder::class,

    // Example seeder with CLI options; safe no-op by default
    'ExampleUserSeeder' => ExampleUserSeeder::class,
];
