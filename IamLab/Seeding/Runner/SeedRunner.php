<?php
declare(strict_types=1);

namespace IamLab\Seeding\Runner;

use IamLab\Seeding\Registry\SeederRegistry;
use IamLab\Seeding\Infrastructure\SeedLogRepositoryInterface;
use IamLab\Seeding\Infrastructure\UnitOfWorkInterface;
use IamLab\Seeding\Support\ClockInterface;
use IamLab\Seeding\Support\ConsolePrinter;

final class SeedRunner
{
    /** @param array<string,mixed> $options */
    public function __construct(
        private SeederRegistry $registry,
        private SeedLogRepositoryInterface $seedLog,
        private UnitOfWorkInterface $uow,
        private ClockInterface $clock,
        private ConsolePrinter $printer,
        private array $options = []
    ) {
    }

    /** @param array<int,class-string> $classes */
    public function run(array $classes, bool $force, bool $dryRun): int
    {
        try {
            $this->seedLog->ensureTable();
        } catch (\Throwable $e) {
            $this->printer->error('Failed ensuring seed_log table: ' . $e->getMessage());
            return 1;
        }

        $batch = $this->seedLog->nextBatchNumber();
        $this->printer->info("Starting seeders (batch {$batch})" . ($dryRun ? ' [DRY-RUN]' : '') . '...');
        $this->printer->info('');

        try {
            $this->uow->begin();

            foreach ($classes as $class) {
                $already = $this->seedLog->isSeeded($class);
                if ($already && !$force) {
                    $this->printer->info("[SKIP] {$class} already seeded. Use --force to re-run.");
                    continue;
                }

                $this->printer->info("[RUN ] {$class}");
                $seeder = $this->registry->instantiate($class, $this->options);
                if (!method_exists($seeder, 'run')) {
                    throw new \RuntimeException("Seeder {$class} missing run() method");
                }

                $seeder->run();

                if (!$dryRun) {
                    $this->seedLog->markSeeded($class, $batch, $this->clock->now());
                }

                $this->printer->info("[DONE] {$class}");
                $this->printer->info('');
            }

            if ($dryRun) {
                $this->uow->rollback();
            } else {
                $this->uow->commit();
            }

            $this->printer->info('Seeding completed successfully!');
            return 0;
        } catch (\Throwable $e) {
            try { $this->uow->rollback(); } catch (\Throwable) {}
            $this->printer->error('Error: ' . $e->getMessage());
            if (PHP_SAPI === 'cli' && function_exists('debug_backtrace')) {
                $this->printer->error($e->getTraceAsString());
            }
            return 1;
        }
    }
}
