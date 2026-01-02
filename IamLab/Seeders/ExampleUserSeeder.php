<?php
declare(strict_types=1);

namespace IamLab\Seeders;

use IamLab\Seeding\Contracts\BuildsFromOptions;
use IamLab\Seeding\Contracts\ProvidesCliOptions;
use IamLab\Seeding\Contracts\SeederInterface;

/**
 * Example seeder demonstrating options + idempotency pattern.
 * This example does NOT perform DB writes; it prints intended actions.
 * Replace with your own DAO/repository to persist changes.
 */
final class ExampleUserSeeder implements SeederInterface, ProvidesCliOptions, BuildsFromOptions
{
    public function __construct(
        private ?string $email = null,
        private ?string $password = null,
        private ?string $name = null
    ) {
    }

    public static function cliOptions(): array
    {
        return ['email:', 'password:', 'name:'];
    }

    /** @param array<string,mixed> $options */
    public static function fromOptions(array $options): self
    {
        $email = isset($options['email']) ? (string) $options['email'] : null;
        $password = isset($options['password']) ? (string) $options['password'] : null;
        $name = isset($options['name']) ? (string) $options['name'] : null;
        return new self($email, $password, $name);
    }

    public function run(): void
    {
        $email = $this->email ?? 'admin@example.com';
        $password = $this->password ?? 'secret123';
        $name = $this->name ?? explode('@', $email)[0];

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email provided: ' . $email);
        }
        if (strlen($password) < 6) {
            throw new \InvalidArgumentException('Password must be at least 6 characters long.');
        }

        // In a real implementation, check if user exists and upsert. Here we just print actions.
        fwrite(STDOUT, "[INFO] Would ensure user exists: {$email} (name={$name})\n");
        fwrite(STDOUT, "[INFO] Would hash password and save user record\n");
    }
}
