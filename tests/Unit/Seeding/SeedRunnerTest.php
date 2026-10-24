<?php

declare(strict_types=1);

namespace Tests\Unit\Seeding;

use IamLab\Seeding\Infrastructure\SeedLogRepositoryInterface;
use IamLab\Seeding\Infrastructure\UnitOfWorkInterface;
use IamLab\Seeding\Registry\SeederRegistry;
use IamLab\Seeding\Runner\SeedRunner;
use IamLab\Seeding\Support\ConsolePrinter;
use IamLab\Seeding\Support\SystemClock;
use PHPUnit\Framework\TestCase;
use RuntimeException;

// phpcs:disable PSR1.Files.SideEffects -- Load the test seeder fixture for registry instantiation.
require_once __DIR__ . '/FailingTestSeeder.php';

final class SeedRunnerTest extends TestCase
{
    public function testFailedSeederRollsBackAndIsNotMarkedSeeded(): void
    {
        $seedLog = $this->createMock(SeedLogRepositoryInterface::class);
        $seedLog->expects($this->once())->method('ensureTable');
        $seedLog->expects($this->once())->method('nextBatchNumber')->willReturn(1);
        $seedLog->expects($this->once())
            ->method('isSeeded')
            ->with(FailingTestSeeder::class)
            ->willReturn(false);
        $seedLog->expects($this->never())->method('markSeeded');

        $unitOfWork = $this->createMock(UnitOfWorkInterface::class);
        $unitOfWork->expects($this->once())->method('begin');
        $unitOfWork->expects($this->once())->method('rollback');
        $unitOfWork->expects($this->never())->method('commit');

        $runner = new SeedRunner(
            new SeederRegistry([FailingTestSeeder::class => FailingTestSeeder::class]),
            $seedLog,
            $unitOfWork,
            new SystemClock(),
            new ConsolePrinter()
        );

        $this->assertSame(1, $runner->run([FailingTestSeeder::class], false, false));
    }
}
