<?php
namespace IamLab\Commands;

use IamLab\Core\Command\BaseCommand;

class ProjectInitCommand extends BaseCommand
{
    public function getSignature(): string
    {
        return 'project:init {name?} {namespace?} [--force]';
    }

    public function getDescription(): string
    {
        return 'Initialize a new project from this stub by renaming the project and namespace';
    }

    public function getHelp(): string
    {
        return <<<HELP
The project:init command helps you set up a new project based on this stub.
It will:
  1. Rename the 'IamLab' namespace to your desired namespace.
  2. Update 'composer.json' and 'package.json' with your project name.
  3. Rename the 'IamLab' directory to match your new namespace.
  4. Create a .env file if it doesn't exist.

Usage:
  ./phalcons command project:init [name] [namespace] [--force]

Options:
  --force             Skip confirmation prompt

Example:
  ./phalcons command project:init my-new-project MyNewProject --force
HELP;
    }

    protected function handle(): int
    {
        $projectName = $this->argument(0, '');
        if (empty($projectName)) {
            $projectName = $this->ask('Enter your new project name (slug, e.g., my-new-project):', 'my-new-project');
        }

        $namespace = $this->argument(1, '');
        if (empty($namespace)) {
            $defaultNamespace = str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $projectName)));
            $namespace = $this->ask("Enter your new project namespace (e.g., $defaultNamespace):", $defaultNamespace);
        }

        $this->info("Initializing project: $projectName with namespace: $namespace...");

        if (!$this->hasOption('force') && !$this->confirm("This will modify files in the current directory. Are you sure you want to continue?", true)) {
            $this->warn('Project initialization cancelled.');
            return 0;
        }

        // 1. Rename occurrences in files
        $this->replaceNamespace('IamLab', $namespace);
        
        // 2. Update composer.json
        $this->updateComposerJson($projectName, $namespace);
        
        // 3. Update package.json
        $this->updatePackageJson($projectName);

        // 4. Create .env from .env.example if not exists
        if (!file_exists('.env') && file_exists('.env.example')) {
            copy('.env.example', '.env');
            $this->success("Created .env from .env.example");
        }

        // 5. Rename IamLab directory
        if (is_dir('IamLab')) {
            rename('IamLab', $namespace);
            $this->success("Renamed IamLab directory to $namespace");
        }

        $this->success("\nProject initialized successfully!");
        $this->info("Next steps:");
        $this->line("1. Run: composer update");
        $this->line("2. Run: ./phalcons up -d");
        $this->line("3. Run: ./phalcons migrate");
        
        return 0;
    }

    private function replaceNamespace(string $oldNamespace, string $newNamespace): void
    {
        $this->info("Replacing namespace $oldNamespace with $newNamespace in files...");
        
        $files = $this->getFilesToProcess();
        $progressBar = $this->progressBar(count($files));
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $newContent = str_replace($oldNamespace, $newNamespace, $content);
            
            if ($newContent !== $content) {
                file_put_contents($file, $newContent);
            }
            $progressBar->advance(1);
        }
        $progressBar->finish();
        $this->success("\nNamespace replacement completed.");
    }

    private function getFilesToProcess(): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator('.', \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        $files = [];
        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            
            $path = $file->getPathname();
            
            // Skip vendor, node_modules, and some other directories
            if (str_contains($path, './vendor/') || 
                str_contains($path, './node_modules/') || 
                str_contains($path, './.git/') ||
                str_contains($path, './_docs/') ||
                str_contains($path, './docs/')) {
                continue;
            }
            
            // Only process relevant file extensions
            if (preg_match('/\.(php|js|json|css|md|html|yml|yaml|xml|example)$/', $path)) {
                $files[] = $path;
            }
        }
        
        return $files;
    }

    private function updateComposerJson(string $projectName, string $namespace): void
    {
        if (!file_exists('composer.json')) return;
        
        $composer = json_decode(file_get_contents('composer.json'), true);
        $composer['name'] = "iam-lab/$projectName";
        $composer['description'] = "New project generated from IamLab Phalcon Stub";
        
        if (isset($composer['autoload']['psr-4']['IamLab\\'])) {
            $composer['autoload']['psr-4'][$namespace . '\\'] = $namespace . '/';
            unset($composer['autoload']['psr-4']['IamLab\\']);
        }

        if (isset($composer['autoload']['files'])) {
            foreach ($composer['autoload']['files'] as &$file) {
                $file = str_replace('IamLab/', $namespace . '/', $file);
            }
        }

        if (isset($composer['autoload']['include-path'])) {
            foreach ($composer['autoload']['include-path'] as &$path) {
                $path = str_replace('IamLab/', $namespace . '/', $path);
            }
        }
        
        file_put_contents('composer.json', json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->success("Updated composer.json");
    }

    private function updatePackageJson(string $projectName): void
    {
        if (!file_exists('package.json')) return;
        
        $package = json_decode(file_get_contents('package.json'), true);
        $package['name'] = $projectName;
        
        file_put_contents('package.json', json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->success("Updated package.json");
    }
}
