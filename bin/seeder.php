#!/usr/bin/env php
<?php
declare(strict_types=1);

// Unified Seeder CLI for IamLab project

use IamLab\Migrations\Seeders\RolesSeeder;
use IamLab\Migrations\Seeders\SiteSettingsSeeder;
use IamLab\Seeding\Infrastructure\PdoSeedLogRepository;
use IamLab\Seeding\Infrastructure\PdoUnitOfWork;
use IamLab\Seeding\Registry\SeederRegistry;
use IamLab\Seeding\Runner\SeedRunner;
use IamLab\Seeding\Support\ConsolePrinter;
use IamLab\Seeding\Support\SystemClock;
use Phalcon\Di\FactoryDefault;
use Phalcon\Di\Di;
use function App\Core\Helpers\loadEnv;

// Paths
define('APP_PATH', realpath(__DIR__ . '/../IamLab'));
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// Autoload
require_once ROOT_PATH . '/vendor/autoload.php';

// Load env
loadEnv(ROOT_PATH . '/.env');

// Error reporting (optional)
if (\App\Core\Helpers\env('APP_DEBUG') === 'debug') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
}

// Registered seeders: map short names to FQCNs (moved to config file)
$seedersConfigPath = APP_PATH . '/config/seeders.php';
if (!is_file($seedersConfigPath)) {
    fwrite(STDERR, "[ERROR] Seeder registry config not found at {$seedersConfigPath}\n");
    exit(1);
}
/** @var array<string,string> $registeredSeeders */
$registeredSeeders = require $seedersConfigPath;
if (!is_array($registeredSeeders)) {
    fwrite(STDERR, "[ERROR] Seeder registry config must return an array.\n");
    exit(1);
}

// Base CLI options
$baseOptions = [
    'all',
    'seed:',      // comma-separated list
    'force',
    'list',
    'dry-run',
    'verbose',
];

// Collect dynamic options from seeders that provide them
$registryForOptions = new SeederRegistry($registeredSeeders);
$dynamicOptions = $registryForOptions->collectCliOptions();
$optionDefs = array_values(array_unique(array_merge($baseOptions, $dynamicOptions)));

$options = getopt('', $optionDefs) ?: [];
$verbose = isset($options['verbose']);
$printer = new ConsolePrinter($verbose);

if (isset($options['list'])) {
    $printer->info('Available seeders and their CLI options:');
    foreach ($registeredSeeders as $short => $fqcn) {
        $opts = method_exists($fqcn, 'cliOptions') ? (array) $fqcn::cliOptions() : [];
        $optsStr = $opts ? implode(', ', $opts) : '—';
        $printer->info(" - {$short} ({$fqcn}) options: {$optsStr}");
    }
    $printer->info('');
    $printer->info('Global options:');
    $printer->info(' - --all');
    $printer->info(' - --seed=Class1,Class2');
    $printer->info(' - --force');
    $printer->info(' - --list');
    $printer->info(' - --dry-run');
    $printer->info(' - --verbose');
    exit(0);
}

// Bootstrap DI and obtain PDO, or fallback to env-based PDO when Phalcon isn't available
$pdo = null;
if (class_exists('Phalcon\\Di\\FactoryDefault')) {
    // Phalcon available: use project DB adapter
    $di = new FactoryDefault();
    include APP_PATH . '/config/services.php';
    $di->getLoader();
    Di::setDefault($di);

    $db = $di->get('db');
    if (!method_exists($db, 'getInternalHandler')) {
        $printer->error('Database adapter does not expose an internal PDO handler.');
        exit(1);
    }
    /** @var PDO $pdo */
    $pdo = $db->getInternalHandler();
} else {
    // Fallback: create PDO using environment variables
    $dsn = getenv('DB_DSN') ?: '';
    $user = getenv('DB_USER') ?: '';
    $pass = getenv('DB_PASSWORD') ?: '';
    if ($dsn === '') {
        $printer->error('Missing DB_DSN env var. Example: mysql:host=127.0.0.1;dbname=app;charset=utf8mb4');
        $printer->info('Hint: Prefer running inside Docker with ./phalcons migrate:seed so Phalcon DI is available.');
        exit(1);
    }
    try {
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Throwable $e) {
        $printer->error('Failed to connect to database: ' . $e->getMessage());
        exit(1);
    }
}

// Resolve selected seeders
$selectedClasses = [];
if (!empty($options['seed'])) {
    $parts = array_filter(array_map('trim', explode(',', (string) $options['seed'])));
    foreach ($parts as $name) {
        $fqcn = $registeredSeeders[$name] ?? (class_exists($name) ? $name : null);
        if ($fqcn) {
            $selectedClasses[$fqcn] = $fqcn;
        } else {
            $printer->warn("Warning: Seeder '{$name}' not found, skipping.");
        }
    }
} elseif (isset($options['all'])) {
    foreach ($registeredSeeders as $fqcn) {
        $selectedClasses[$fqcn] = $fqcn;
    }
} else {
    // Default to all if no specific seeders requested
    foreach ($registeredSeeders as $fqcn) {
        $selectedClasses[$fqcn] = $fqcn;
    }
}

if (!$selectedClasses) {
    $printer->warn('No seeders selected. Use --all or --seed=Class1,Class2 or --list to view options.');
    exit(0);
}

$force = isset($options['force']);
$dryRun = isset($options['dry-run']);

$uow = new PdoUnitOfWork($pdo);
$seedLog = new PdoSeedLogRepository($pdo);
$clock = new SystemClock();
$registry = new SeederRegistry($registeredSeeders);
$runner = new SeedRunner($registry, $seedLog, $uow, $clock, $printer, $options);

$exitCode = $runner->run(array_values($selectedClasses), $force, $dryRun);
exit($exitCode);