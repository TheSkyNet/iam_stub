<?php
declare(strict_types=1);

namespace IamLab\Seeding\Infrastructure;

interface UnitOfWorkInterface
{
    public function begin(): void;
    public function commit(): void;
    public function rollback(): void;
    public function isActive(): bool;
}
