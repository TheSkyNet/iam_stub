<?php
declare(strict_types=1);

namespace IamLab\Seeding\Infrastructure;

use PDO;
use PDOException;

final class PdoSeedLogRepository implements SeedLogRepositoryInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function ensureTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS seed_log (
            id INT AUTO_INCREMENT PRIMARY KEY,
            class VARCHAR(191) NOT NULL UNIQUE,
            batch INT NOT NULL,
            seeded_at DATETIME NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
        $this->pdo->exec($sql);
    }

    public function isSeeded(string $class): bool
    {
        $stmt = $this->pdo->prepare('SELECT 1 FROM seed_log WHERE class = ? LIMIT 1');
        $stmt->execute([$class]);
        return (bool) $stmt->fetchColumn();
    }

    public function markSeeded(string $class, int $batch, \DateTimeImmutable $now): void
    {
        if ($this->isSeeded($class)) {
            $stmt = $this->pdo->prepare('UPDATE seed_log SET batch = ?, seeded_at = ? WHERE class = ?');
            $stmt->execute([$batch, $now->format('Y-m-d H:i:s'), $class]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO seed_log (class, batch, seeded_at) VALUES (?,?,?)');
            $stmt->execute([$class, $batch, $now->format('Y-m-d H:i:s')]);
        }
    }

    public function nextBatchNumber(): int
    {
        try {
            $row = $this->pdo->query('SELECT MAX(batch) AS max_batch FROM seed_log')->fetch(PDO::FETCH_ASSOC);
            $max = 0;
            if (is_array($row)) {
                $max = (int) ($row['max_batch'] ?? 0);
            }
            return $max + 1;
        } catch (PDOException) {
            // If table does not exist yet, ensure and return 1
            $this->ensureTable();
            return 1;
        }
    }
}
