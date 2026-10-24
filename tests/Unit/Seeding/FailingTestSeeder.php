<?php

declare(strict_types=1);

namespace Tests\Unit\Seeding;

use RuntimeException;

final class FailingTestSeeder
{
    public function run(): void
    {
        throw new RuntimeException('Expected seeder failure');
    }
}
