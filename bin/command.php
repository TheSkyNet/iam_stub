<?php

/**
 * Phalcon Command Runner
 * 
 * This script provides a command-line interface for running custom commands
 * in the Phalcon application. It loads the application environment and 
 * services, then executes registered commands with argument parsing.
 */

// Bootstrap the application like index.php
require_once __DIR__ . '/../vendor/autoload.php';

use Phalcon\Di\FactoryDefault;
use function App\Core\Helpers\loadEnv;

// Define constants
define('APP_PATH', realpath(__DIR__ . '/../IamLab'));
define('ROOT_PATH', realpath(__DIR__ . '/..'));

// Load environment variables
loadEnv(ROOT_PATH . '/.env');

// Set up error reporting for CLI
if (\App\Core\Helpers\env('APP_DEBUG') == 'true') {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Create DI container
$di = new FactoryDefault();

// Include services
include APP_PATH . '/config/services.php';

// Call the autoloader service
$di->getLoader();

// Make DI globally available
$GLOBALS['di'] = $di;

/**
 * Command Runner Class
 */
class CommandRunner
{
    private array $commands = [];
    private array $argv;
    private string $commandsConfigPath;

    public function __construct(array $argv)
    {
        $this->argv = $argv;
        $this->commandsConfigPath = APP_PATH . '/config/commands.php';
        $this->loadCommands();
    }

    /**
     * Load registered commands from config
     */
    private function loadCommands(): void
    {
        if (file_exists($this->commandsConfigPath)) {
            $this->commands = include $this->commandsConfigPath;
        }
    }

    /**
     * Run the command runner
     */
    public function run(): void
    {
        // Remove script name from arguments
        array_shift($this->argv);

        if (empty($this->argv)) {
            $this->showHelp();
            return;
        }

        $commandName = $this->argv[0];

        // Handle built-in commands
        switch ($commandName) {
            case 'list':
                $this->listCommands();
                break;
            case 'help':
                $this->showHelp();
                break;
            default:
                $this->executeCommand($commandName);
                break;
        }
    }

    /**
     * Execute a registered command
     */
    private function executeCommand(string $commandName): void
    {
        if (!isset($this->commands[$commandName])) {
            $this->error("Command '{$commandName}' not found. Use 'list' to see available commands.");
            return;
        }

        $commandConfig = $this->commands[$commandName];
        $commandClass = $commandConfig['class'];

        if (!class_exists($commandClass)) {
            $this->error("Command class '{$commandClass}' not found.");
            return;
        }

        try {
            // Create command instance and inject DI
            $command = new $commandClass();
            if (method_exists($command, 'setDI')) {
                $command->setDI($GLOBALS['di']);
            }

            if (!method_exists($command, 'execute')) {
                $this->error("Command class '{$commandClass}' must have an 'execute' method.");
                return;
            }

            // Parse arguments and options
            $parsed = $this->parseArguments(array_slice($this->argv, 1));

            // Execute the command with proper format
            $exitCode = $command->execute($parsed['arguments'], $parsed['options']);

            // Exit with the command's exit code
            if ($exitCode !== 0) {
                exit($exitCode);
            }

        } catch (Exception $e) {
            $this->error("Error executing command '{$commandName}': " . $e->getMessage());
        }
    }

    /**
     * Parse command arguments and options
     */
    private function parseArguments(array $args): array
    {
        $arguments = [];
        $options = [];

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args[$i];

            if (str_starts_with($arg, '--')) {
                // Long option (--name=value or --flag)
                if (str_contains($arg, '=')) {
                    [$key, $value] = explode('=', substr($arg, 2), 2);
                    $options[$key] = $value;
                } else {
                    $key = substr($arg, 2);
                    // Check if next argument is a value (not starting with -)
                    if (isset($args[$i + 1]) && !str_starts_with($args[$i + 1], '-')) {
                        $options[$key] = $args[++$i];
                    } else {
                        $options[$key] = true;
                    }
                }
            } elseif (str_starts_with($arg, '-') && strlen($arg) > 1) {
                // Short option(s) (-d, -v, -dv, etc.)
                $flags = str_split(substr($arg, 1));
                foreach ($flags as $flag) {
                    $options[$flag] = true;
                }
            } else {
                // Regular argument
                $arguments[] = $arg;
            }
        }

        return [
            'arguments' => $arguments,
            'options' => $options
        ];
    }

    /**
     * List all available commands
     */
    private function listCommands(): void
    {
        $this->output("Available Commands:");
        $this->output("==================");

        if (empty($this->commands)) {
            $this->output("No commands registered.");
            return;
        }

        foreach ($this->commands as $name => $config) {
            $description = $config['description'] ?? 'No description available';
            $this->output(sprintf("  %-20s %s", $name, $description));
        }

        $this->output("");
        $this->output("Built-in Commands:");
        $this->output("  list                 List all available commands");
        $this->output("  help                 Show this help message");
    }

    /**
     * Show help message
     */
    private function showHelp(): void
    {
        $this->output("Phalcon Command Runner");
        $this->output("=====================");
        $this->output("");
        $this->output("Usage:");
        $this->output("  ./phalcons command <command-name> [arguments] [options]");
        $this->output("");
        $this->output("Examples:");
        $this->output("  ./phalcons command test:mail bob -d -v --name=steve");
        $this->output("  ./phalcons command list");
        $this->output("  ./phalcons command help");
        $this->output("");
        $this->output("Options:");
        $this->output("  -d, -v              Short flags");
        $this->output("  --name=value        Long options with values");
        $this->output("  --flag              Long flags");
        $this->output("");
        $this->listCommands();
    }

    /**
     * Output a message
     */
    private function output(string $message): void
    {
        echo $message . PHP_EOL;
    }

    /**
     * Output an error message
     */
    private function error(string $message): void
    {
        fwrite(STDERR, "Error: " . $message . PHP_EOL);
        exit(1);
    }
}

// Run the command runner
$runner = new CommandRunner($argv);
$runner->run();
