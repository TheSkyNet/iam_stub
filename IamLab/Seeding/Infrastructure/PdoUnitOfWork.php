<?php

declare(strict_types=1);

namespace IamLab\Seeding\Infrastructure;

use PDO;

final readonly class PdoUnitOfWork implements UnitOfWorkInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    #[\Override]
    public function begin(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    #[\Override]
    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    #[\Override]
    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    #[\Override]
    public function isActive(): bool
    {
        return $this->pdo->inTransaction();
    }
}
