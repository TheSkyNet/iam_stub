#!/usr/bin/env php
<?php

/**
 * Zephir Extension Compiler
 * 
 * This script compiles all Zephir extensions found in the etc/ directory
 */

class ZephirCompiler
{
    private $projectRoot;
    private $etcDir;
    private $verbose;

    public function __construct($projectRoot = null, $verbose = false)
    {
        $this->projectRoot = $projectRoot ?: dirname(__DIR__);
        $this->etcDir = $this->projectRoot . '/etc';
        $this->verbose = $verbose;
    }

    public function compile()
    {
        $this->log("Starting Zephir extension compilation...");

        if (!is_dir($this->etcDir)) {
            $this->log("No etc/ directory found. Skipping Zephir compilation.");
            return true;
        }

        $extensions = $this->findExtensions();

        if (empty($extensions)) {
            $this->log("No Zephir extensions found in etc/ directory.");
            return true;
        }

        $success = true;
        foreach ($extensions as $extension) {
            $this->log("Compiling extension: {$extension}");
            if (!$this->compileExtension($extension)) {
                $this->log("Failed to compile extension: {$extension}", true);
                $success = false;
            } else {
                $this->log("Successfully compiled extension: {$extension}");
            }
        }

        return $success;
    }

    private function findExtensions()
    {
        $extensions = [];
        $dirs = glob($this->etcDir . '/*', GLOB_ONLYDIR);

        foreach ($dirs as $dir) {
            $extensionName = basename($dir);
            $configFile = $dir . '/config.json';

            // Check if config.json exists
            if (!file_exists($configFile)) {
                continue;
            }

            // Read config to get namespace
            $config = json_decode(file_get_contents($configFile), true);
            if (!$config || !isset($config['namespace'])) {
                continue;
            }

            $namespace = $config['namespace'];
            $namespaceDir = $dir . '/' . $namespace;

            // Check if namespace directory exists with .zep files
            if (is_dir($namespaceDir)) {
                $zepFiles = glob($namespaceDir . '/*.zep');
                if (!empty($zepFiles)) {
                    $extensions[] = $extensionName;
                }
            }
        }

        return $extensions;
    }

    private function compileExtension($extensionName)
    {
        $extensionDir = $this->etcDir . '/' . $extensionName;
        $originalDir = getcwd();

        try {
            // Change to extension directory
            chdir($extensionDir);

            // Check if zephir is available
            if (!$this->isZephirAvailable()) {
                $this->log("Zephir is not available. Please install Zephir first.", true);
                return false;
            }

            // Clean previous builds
            $this->executeCommand('zephir clean');

            // Generate the extension
            $result = $this->executeCommand('zephir generate');
            if ($result !== 0) {
                $this->log("Failed to generate extension code for {$extensionName}", true);
                return false;
            }

            // Compile the extension
            $result = $this->executeCommand('zephir compile');
            if ($result !== 0) {
                $this->log("Failed to compile extension {$extensionName}", true);
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->log("Exception while compiling {$extensionName}: " . $e->getMessage(), true);
            return false;
        } finally {
            // Always return to original directory
            chdir($originalDir);
        }
    }

    private function getZephirPath()
    {
        $result = null;
        $output = [];

        // First try to find zephir in PATH
        exec('which zephir 2>/dev/null', $output, $result);
        if ($result === 0 && !empty($output)) {
            return trim($output[0]);
        }

        // Try to find zephir in global composer directory
        $composerHome = getenv('COMPOSER_HOME') ?: (getenv('HOME') . '/.composer');
        $zephirPath = $composerHome . '/vendor/bin/zephir';
        if (file_exists($zephirPath) && is_executable($zephirPath)) {
            return $zephirPath;
        }

        // Try common global composer locations
        $globalPaths = [
            '/usr/local/bin/zephir',
            '/usr/local/composer/vendor/bin/zephir',
            '/root/.composer/vendor/bin/zephir',
            '/home/phalcons/.composer/vendor/bin/zephir',
            getenv('HOME') . '/.config/composer/vendor/bin/zephir'
        ];

        foreach ($globalPaths as $path) {
            if (file_exists($path) && is_executable($path)) {
                return $path;
            }
        }

        return null;
    }

    private function isZephirAvailable()
    {
        return $this->getZephirPath() !== null;
    }

    private function executeCommand($command)
    {
        // Replace 'zephir' with full path if needed
        if (strpos($command, 'zephir') === 0) {
            $zephirPath = $this->getZephirPath();
            if ($zephirPath && $zephirPath !== 'zephir') {
                $command = str_replace('zephir', $zephirPath, $command);
            }
        }

        $this->log("Executing: {$command}");

        $result = null;
        $output = [];
        exec($command . ' 2>&1', $output, $result);

        if ($this->verbose || $result !== 0) {
            foreach ($output as $line) {
                $this->log("  " . $line);
            }
        }

        return $result;
    }

    private function log($message, $isError = false)
    {
        $prefix = $isError ? '[ERROR] ' : '[INFO] ';
        $stream = $isError ? STDERR : STDOUT;
        fwrite($stream, $prefix . $message . PHP_EOL);
    }
}

// Main execution
if (php_sapi_name() === 'cli') {
    $verbose = in_array('-v', $argv) || in_array('--verbose', $argv);
    $compiler = new ZephirCompiler(null, $verbose);

    $success = $compiler->compile();
    exit($success ? 0 : 1);
}
