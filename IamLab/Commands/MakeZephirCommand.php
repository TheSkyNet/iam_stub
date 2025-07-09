<?php

namespace IamLab\Commands;

use IamLab\Core\Command\BaseCommand;

/**
 * Command to generate new Zephir extensions
 */
class MakeZephirCommand extends BaseCommand
{
    /**
     * Get command signature
     */
    public function getSignature(): string
    {
        return 'make:zephir {name} {--class=} {--v|verbose}';
    }

    /**
     * Get command description
     */
    public function getDescription(): string
    {
        return 'Generate a new Zephir extension with proper structure';
    }

    /**
     * Get command help
     */
    public function getHelp(): string
    {
        return <<<HELP
Generate a new Zephir extension with the proper directory structure, configuration, and basic class template.

Usage:
  ./phalcons command make:zephir <name> [options]

Arguments:
  name                    Name of the Zephir extension (e.g., MyExtension, Utils, Helper)

Options:
  --class=CLASS          Name of the initial class to create (defaults to Utils)
  -v, --verbose          Enable verbose output

Examples:
  ./phalcons command make:zephir MyExtension                    # Generate MyExtension with Utils class
  ./phalcons command make:zephir Calculator --class=Math       # Generate Calculator with Math class
  ./phalcons command make:zephir Helper -v                     # Generate with verbose output

This command will create:
  - etc/ExtensionName/config.json          # Zephir configuration
  - etc/ExtensionName/ExtensionName/        # Namespace directory
  - etc/ExtensionName/ExtensionName/ClassName.zep  # Basic Zephir class
  - etc/ExtensionName/.gitignore           # Git ignore file for compiled files
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    protected function handle(): int
    {
        $name = $this->argument(0);
        if (!$name) {
            $this->error("Extension name is required");
            return 1;
        }

        // Validate and format name
        $name = $this->formatName($name);
        $this->verbose("Formatted extension name: {$name}");

        $className = $this->option('class', 'Utils');
        $className = $this->formatName($className);
        $this->verbose("Class name: {$className}");

        $this->info("Generating Zephir extension: {$name}");

        try {
            $this->generateExtensionStructure($name, $className);
            $this->success("Successfully generated Zephir extension {$name}");
            $this->info("Next steps:");
            $this->info("1. Edit etc/{$name}/{$name}/{$className}.zep to add your methods");
            $this->info("2. Run './phalcons build' to compile the extension");
            $this->info("3. Use the extension in PHP: use {$name}\\{$className};");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error generating extension: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Format extension/class name
     */
    protected function formatName(string $name): string
    {
        // Remove any non-alphanumeric characters and capitalize first letter of each word
        $name = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        return ucfirst($name);
    }

    /**
     * Generate the complete extension structure
     */
    private function generateExtensionStructure(string $name, string $className): void
    {
        $extensionDir = "etc/{$name}";
        $namespaceDir = "{$extensionDir}/{$name}";

        // Create directories
        $this->info("Creating directory structure...");
        if (!is_dir($namespaceDir)) {
            mkdir($namespaceDir, 0755, true);
            $this->verbose("Created directory: {$namespaceDir}");
        }

        // Generate config.json
        $this->generateConfig($name, $extensionDir);

        // Generate .zep class file
        $this->generateZepClass($name, $className, $namespaceDir);

        // Generate .gitignore
        $this->generateGitignore($extensionDir);
    }

    /**
     * Generate config.json file
     */
    private function generateConfig(string $name, string $extensionDir): void
    {
        $this->info("Generating config.json...");

        $configContent = $this->getConfigTemplate($name);
        $configPath = "{$extensionDir}/config.json";

        $this->createFile($configPath, $configContent);
        $this->verbose("Created config: {$configPath}");
    }

    /**
     * Generate Zephir class file
     */
    private function generateZepClass(string $name, string $className, string $namespaceDir): void
    {
        $this->info("Generating {$className}.zep class...");

        $classContent = $this->getZepClassTemplate($name, $className);
        $classPath = "{$namespaceDir}/{$className}.zep";

        $this->createFile($classPath, $classContent);
        $this->verbose("Created class: {$classPath}");
    }

    /**
     * Generate .gitignore file
     */
    private function generateGitignore(string $extensionDir): void
    {
        $this->info("Generating .gitignore...");

        $gitignoreContent = $this->getGitignoreTemplate();
        $gitignorePath = "{$extensionDir}/.gitignore";

        $this->createFile($gitignorePath, $gitignoreContent);
        $this->verbose("Created .gitignore: {$gitignorePath}");
    }

    /**
     * Create file with content
     */
    private function createFile(string $path, string $content): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (file_exists($path)) {
            if (!$this->confirm("File {$path} already exists. Overwrite?", false)) {
                $this->warn("Skipped: {$path}");
                return;
            }
        }

        file_put_contents($path, $content);
    }

    /**
     * Get config.json template
     */
    protected function getConfigTemplate(string $name): string
    {
        $lowerName = strtolower($name);

        return <<<JSON
{
    "namespace": "{$name}",
    "name": "{$lowerName}",
    "description": "A Zephir extension for {$name}",
    "author": "Developer",
    "version": "1.0.0",
    "verbose": false,
    "requires": {
        "php": ">=7.0"
    },
    "stubs": {
        "path": "ide/%version%/%namespace%/",
        "stubs-run-after-generate": false
    },
    "api": {
        "path": "doc/%version%",
        "theme": {
            "name": "zephir",
            "options": {
                "github": "",
                "analytics": null
            }
        }
    },
    "warnings": {
        "unused-variable": true,
        "unused-variable-external": false,
        "possible-wrong-parameter-undefined": false,
        "nonexistent-function": true,
        "nonexistent-class": true,
        "non-valid-isset": true,
        "non-array-update": true,
        "non-valid-objectupdate": true,
        "non-valid-fetch": true,
        "invalid-array-index": true,
        "non-array-append": true,
        "invalid-return-type": true,
        "unreachable-code": true,
        "nonexistent-constant": true,
        "not-supported-magic-constant": true,
        "non-valid-decrement": true,
        "non-valid-increment": true,
        "non-valid-clone": true,
        "non-valid-new": true,
        "non-array-access": true,
        "invalid-reference": true,
        "invalid-typeof-comparison": true,
        "conditional-initialization": true
    },
    "optimizations": {
        "static-type-inference": true,
        "static-type-inference-second-pass": true,
        "local-context-pass": false,
        "constant-folding": true,
        "static-constant-class-folding": true,
        "call-gatherer-pass": true,
        "check-invalid-reads": false,
        "side-effect-detector": true
    }
}
JSON;
    }

    /**
     * Get Zephir class template
     */
    protected function getZepClassTemplate(string $namespace, string $className): string
    {
        return <<<ZEP
namespace {$namespace};

/**
 * {$namespace}\\{$className}
 *
 * A utility class for {$namespace} extension
 */
class {$className}
{
    /**
     * Returns a greeting message
     *
     * @param string name
     * @return string
     */
    public static function greet(string name) -> string
    {
        return "Hello, " . name . " from {$namespace} extension!";
    }

    /**
     * Adds two numbers
     *
     * @param int a
     * @param int b
     * @return int
     */
    public static function add(int a, int b) -> int
    {
        return a + b;
    }

    /**
     * Multiplies two numbers
     *
     * @param int a
     * @param int b
     * @return int
     */
    public static function multiply(int a, int b) -> int
    {
        return a * b;
    }

    /**
     * Checks if a string is empty
     *
     * @param string str
     * @return bool
     */
    public static function isEmpty(string str) -> bool
    {
        return strlen(str) == 0;
    }

    /**
     * Returns the current timestamp
     *
     * @return int
     */
    public static function getCurrentTime() -> int
    {
        return time();
    }

    /**
     * Converts string to uppercase
     *
     * @param string str
     * @return string
     */
    public static function toUpper(string str) -> string
    {
        return strtoupper(str);
    }

    /**
     * Converts string to lowercase
     *
     * @param string str
     * @return string
     */
    public static function toLower(string str) -> string
    {
        return strtolower(str);
    }
}
ZEP;
    }

    /**
     * Get .gitignore template
     */
    protected function getGitignoreTemplate(): string
    {
        return <<<GITIGNORE
# Compiled extension files
/ext
.zephir
compile.log
compile-errors.log

# IDE files
.vscode/
.idea/

# OS files
.DS_Store
Thumbs.db
GITIGNORE;
    }
}
