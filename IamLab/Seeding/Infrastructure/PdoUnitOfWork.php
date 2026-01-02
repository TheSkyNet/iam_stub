<?php
declare(strict_types=1);

namespace IamLab\Seeding\Infrastructure;

use PDO;

final class PdoUnitOfWork implements UnitOfWorkInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function begin(): void
    {
        if (!$this->pdo->inTransaction()) {
            $this->pdo->beginTransaction();
        }
    }

    public function commit(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->commit();
        }
    }

    public function rollback(): void
    {
        if ($this->pdo->inTransaction()) {
            $this->pdo->rollBack();
        }
    }

    public function isActive(): bool
    {
        return $this->pdo->inTransaction();
    }
}
