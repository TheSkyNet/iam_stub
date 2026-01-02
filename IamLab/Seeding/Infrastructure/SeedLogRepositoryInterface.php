<?php
declare(strict_types=1);

namespace IamLab\Seeding\Infrastructure;

interface SeedLogRepositoryInterface
{
    public function ensureTable(): void;
    public function isSeeded(string $class): bool;
    public function markSeeded(string $class, int $batch, \DateTimeImmutable $now): void;
    public function nextBatchNumber(): int;
}
