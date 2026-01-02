Seeding System (SOLID, CLI, Idempotent)

Overview
- Unified CLI runner for seeders
- Global options: --all, --seed=Class1,Class2, --force, --list, --dry-run, --verbose
- Per-seeder options via optional static cliOptions()
- Idempotency tracked in seed_log table
- Transactions with rollback on dry-run

Usage via phalcons (Docker helper)
- ./phalcons migrate:seed --list
- ./phalcons migrate:seed --all
- ./phalcons migrate:seed --seed=RolesSeeder,SiteSettingsSeeder
- ./phalcons migrate:seed --seed=RolesSeeder --force
- ./phalcons migrate:seed --seed=RolesSeeder --dry-run

Usage via PHP directly
- php bin/seeder.php --list
- php bin/seeder.php --all
- php bin/seeder.php --seed=RolesSeeder,SiteSettingsSeeder
- php bin/seeder.php --seed=ExampleUserSeeder --email=admin@example.com --password=secret123 --name=Admin

How it works
- Runner: IamLab\\Seeding\\Runner\\SeedRunner
- Registry: IamLab\\Seeding\\Registry\\SeederRegistry
- Infrastructure: PdoUnitOfWork, PdoSeedLogRepository (auto-creates seed_log)
- Support: SystemClock, ConsolePrinter
- The CLI bootstraps Phalcon DI and obtains PDO from the configured DB adapter; no DB_DSN required

Initial registered seeders
- RolesSeeder (IamLab\\Migrations\\Seeders\\RolesSeeder)
- SiteSettingsSeeder (IamLab\\Migrations\\Seeders\\SiteSettingsSeeder)
- ExampleUserSeeder (IamLab\\Seeders\\ExampleUserSeeder) supporting --email, --password, --name

Writing a new seeder
- Create class with run(): void
- Optional: static cliOptions(): array
- Optional: static fromOptions(array $options): self
- Register it in IamLab/config/seeders.php by adding to the returned array, e.g.:

  ```php
  return [
      'MySeeder' => \IamLab\Seeders\MySeeder::class,
  ];
  ```

Idempotency
- seed_log records class and batch number
- Already-seeded classes are skipped unless --force is used
- Dry-run executes but rolls back at the end

Troubleshooting
- Ensure DI is initialized for CLI (services.php is bootstrapped; DI is set default)
- seed_log table is created automatically if missing
- Use --verbose for additional logs
- If the CLI says the registry config is missing, ensure IamLab/config/seeders.php exists and returns an array mapping names to classes