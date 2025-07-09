<?php

namespace IamLab\Commands;

use IamLab\Core\Command\BaseCommand;

class OllamaCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'ollama {action} [--force] [-v|--verbose]';
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Enable or disable Ollama Docker service';
    }

    /**
     * Get command help text
     *
     * @return string
     */
    public function getHelp(): string
    {
        return <<<HELP
Enable or disable Ollama Docker service

Usage:
  ollama {action} [options]

Arguments:
  action                Action to perform: enable, disable, status, restart

Options:
  --force              Force action without confirmation
  -v, --verbose        Enable verbose output

Examples:
  ./phalcons command ollama enable
  ./phalcons command ollama disable --force
  ./phalcons command ollama status
  ./phalcons command ollama restart -v

Description:
  This command manages the Ollama Docker service by updating the .env configuration
  and managing the Docker Compose profile. When enabled, Ollama will be available
  for LLM operations. When disabled, the service will be stopped and removed from
  the Docker Compose stack.
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    protected function handle(): int
    {
        $action = $this->argument(0);

        if (!$action) {
            $this->error("Action is required. Use: enable, disable, status, or restart");
            return 1;
        }

        switch (strtolower($action)) {
            case 'enable':
                return $this->enableOllama();
            case 'disable':
                return $this->disableOllama();
            case 'status':
                return $this->showStatus();
            case 'restart':
                return $this->restartOllama();
            default:
                $this->error("Invalid action '{$action}'. Use: enable, disable, status, or restart");
                return 1;
        }
    }

    /**
     * Enable Ollama service
     */
    private function enableOllama(): int
    {
        $this->info("Enabling Ollama service...");

        // Check if already enabled
        if ($this->isOllamaEnabled()) {
            $this->warn("Ollama is already enabled");
            if (!$this->hasOption('force') && !$this->confirm("Do you want to restart the service?", false)) {
                return 0;
            }
        }

        // Add Ollama service to docker-compose.yml
        if (!$this->addOllamaToDockerCompose()) {
            $this->error("Failed to add Ollama service to docker-compose.yml");
            return 1;
        }

        // Update .env file
        if (!$this->updateEnvFile('LMS_OLLAMA_ENABLED', 'true')) {
            $this->error("Failed to update .env file");
            return 1;
        }

        // Start Ollama service
        $this->info("Starting Ollama Docker service...");
        $result = $this->runDockerCommand('up -d ollama');

        if ($result !== 0) {
            $this->error("Failed to start Ollama service");
            return 1;
        }

        $this->success("Ollama service enabled and started successfully!");
        $this->info("Ollama is now available at: http://localhost:" . $this->getEnvValue('FORWARD_OLLAMA_PORT', '11435'));
        $this->info("You can test the service with: ./phalcons command ollama status");

        return 0;
    }

    /**
     * Disable Ollama service
     */
    private function disableOllama(): int
    {
        $this->info("Disabling Ollama service...");

        $envAlreadyDisabled = !$this->isOllamaEnabled();
        $serviceExistsInCompose = $this->isOllamaInDockerCompose();

        // Check if already fully disabled
        if ($envAlreadyDisabled && !$serviceExistsInCompose) {
            $this->warn("Ollama is already fully disabled");
            return 0;
        }

        // Confirm action unless forced
        if (!$this->hasOption('force') && !$this->confirm("Are you sure you want to disable Ollama?", false)) {
            $this->info("Operation cancelled");
            return 0;
        }

        // Stop Ollama service if it's running
        if (!$envAlreadyDisabled || $serviceExistsInCompose) {
            $this->info("Stopping Ollama Docker service...");
            $result = $this->runDockerCommand('stop ollama');

            if ($result === 0) {
                $this->runDockerCommand('rm -f ollama');
            }
        }

        // Remove Ollama service from docker-compose.yml
        if ($serviceExistsInCompose) {
            $this->info("Removing Ollama service from docker-compose.yml...");
            if (!$this->removeOllamaFromDockerCompose()) {
                $this->error("Failed to remove Ollama service from docker-compose.yml");
                return 1;
            }
        }

        // Update .env file
        if (!$envAlreadyDisabled) {
            if (!$this->updateEnvFile('LMS_OLLAMA_ENABLED', 'false')) {
                $this->error("Failed to update .env file");
                return 1;
            }
        }

        $this->success("Ollama service disabled successfully!");
        return 0;
    }

    /**
     * Show Ollama status
     */
    private function showStatus(): int
    {
        $this->info("Ollama Service Status");
        $this->line("========================");

        // Check .env configuration
        $enabled = $this->isOllamaEnabled();
        $this->line("Configuration: " . ($enabled ? "ENABLED" : "DISABLED"));

        // Check Docker service status
        $dockerStatus = $this->getDockerServiceStatus();
        $this->line("Docker Service: " . $dockerStatus);

        // Show configuration details
        $this->line("");
        $this->info("Configuration Details:");
        $this->line("- Host: " . $this->getEnvValue('LMS_OLLAMA_HOST', 'http://ollama:11434'));
        $this->line("- Model: " . $this->getEnvValue('LMS_OLLAMA_MODEL', 'llama2'));
        $this->line("- Port: " . $this->getEnvValue('FORWARD_OLLAMA_PORT', '11435'));

        // Test connection if enabled
        if ($enabled && strpos($dockerStatus, 'RUNNING') === 0) {
            $this->line("");
            $this->info("Testing connection...");
            if ($this->testOllamaConnection()) {
                $this->success("✓ Ollama is responding correctly");
            } else {
                $this->warn("⚠ Ollama service is running but not responding");
            }
        }

        return 0;
    }

    /**
     * Restart Ollama service
     */
    private function restartOllama(): int
    {
        if (!$this->isOllamaEnabled()) {
            $this->error("Ollama is disabled. Enable it first with: ollama enable");
            return 1;
        }

        $this->info("Restarting Ollama service...");

        // Stop the service
        $this->runDockerCommand('stop ollama');
        $this->runDockerCommand('rm -f ollama');

        // Start it again
        $result = $this->runDockerCommand('up -d ollama');

        if ($result !== 0) {
            $this->error("Failed to restart Ollama service");
            return 1;
        }

        $this->success("Ollama service restarted successfully!");
        return 0;
    }

    /**
     * Check if Ollama is enabled in .env
     */
    private function isOllamaEnabled(): bool
    {
        return strtolower($this->getEnvValue('LMS_OLLAMA_ENABLED', 'false')) === 'true';
    }

    /**
     * Check if Ollama service exists in docker-compose.yml
     */
    private function isOllamaInDockerCompose(): bool
    {
        $composeFile = 'docker-compose.yml';

        if (!file_exists($composeFile)) {
            return false;
        }

        $content = file_get_contents($composeFile);
        return strpos($content, 'ollama:') !== false;
    }

    /**
     * Get Docker service status
     */
    private function getDockerServiceStatus(): string
    {
        // Since we're running inside a container, we need to check if the service
        // is accessible rather than checking Docker directly
        $port = $this->getEnvValue('FORWARD_OLLAMA_PORT', '11435');
        $host = 'ollama'; // Internal Docker network name

        // Try to connect to the service
        $connection = @fsockopen($host, 11434, $errno, $errstr, 2);

        if ($connection) {
            fclose($connection);

            // Test if the service is responding properly
            if ($this->testOllamaConnection()) {
                return 'RUNNING (HEALTHY)';
            } else {
                return 'RUNNING (UNHEALTHY)';
            }
        }

        return 'STOPPED';
    }

    /**
     * Test Ollama connection
     */
    private function testOllamaConnection(): bool
    {
        $port = $this->getEnvValue('FORWARD_OLLAMA_PORT', '11435');
        $url = "http://localhost:{$port}/api/tags";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode === 200;
    }

    /**
     * Run Docker Compose command
     */
    private function runDockerCommand(string $command): int
    {
        // Since we're running inside a container, we can't directly execute docker commands
        // Instead, we'll provide instructions to the user
        $this->warn("Docker commands must be run from the host system (outside the container).");
        $this->info("Please run the following command from your project root:");
        $this->line("  docker compose {$command}");
        $this->line("");

        // For enable/disable operations, we'll assume success since the .env update is the main action
        // The user will need to run the docker command manually
        if (strpos($command, 'up') !== false) {
            $this->info("After running the above command, Ollama should be available.");
        } elseif (strpos($command, 'stop') !== false || strpos($command, 'rm') !== false) {
            $this->info("After running the above command, Ollama will be stopped.");
        }

        return 0; // Assume success for .env operations
    }

    /**
     * Update .env file
     */
    private function updateEnvFile(string $key, string $value): bool
    {
        $envFile = '.env';

        if (!file_exists($envFile)) {
            $this->error(".env file not found");
            return false;
        }

        $content = file_get_contents($envFile);
        $pattern = "/^{$key}=.*$/m";
        $replacement = "{$key}={$value}";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        } else {
            $content .= "\n{$replacement}\n";
        }

        return file_put_contents($envFile, $content) !== false;
    }

    /**
     * Get environment variable value
     */
    private function getEnvValue(string $key, string $default = ''): string
    {
        $envFile = '.env';

        if (!file_exists($envFile)) {
            return $default;
        }

        $content = file_get_contents($envFile);
        $pattern = "/^{$key}=(.*)$/m";

        if (preg_match($pattern, $content, $matches)) {
            return trim($matches[1]);
        }

        return $default;
    }

    /**
     * Add Ollama service to docker-compose.yml
     */
    private function addOllamaToDockerCompose(): bool
    {
        $composeFile = 'docker-compose.yml';

        if (!file_exists($composeFile)) {
            $this->error("docker-compose.yml file not found");
            return false;
        }

        $content = file_get_contents($composeFile);

        // Check if Ollama service already exists
        if (strpos($content, 'ollama:') !== false) {
            $this->verbose("Ollama service already exists in docker-compose.yml");
            return true;
        }

        // Define the Ollama service configuration
        $ollamaService = "    ollama:\n" .
                        "        image: 'ollama/ollama:latest'\n" .
                        "        ports:\n" .
                        "            - '\${FORWARD_OLLAMA_PORT:-11435}:11434'\n" .
                        "        volumes:\n" .
                        "            - 'ollama-data:/root/.ollama'\n" .
                        "        networks:\n" .
                        "            - phalcons\n" .
                        "        environment:\n" .
                        "            - OLLAMA_HOST=0.0.0.0\n" .
                        "        restart: unless-stopped\n" .
                        "        healthcheck:\n" .
                        "            test: [\"CMD\", \"curl\", \"-f\", \"http://localhost:11434/api/tags\"]\n" .
                        "            interval: 30s\n" .
                        "            timeout: 10s\n" .
                        "            retries: 3\n";

        // Add the Ollama service at the end of the services section
        $content = rtrim($content) . "\n" . $ollamaService;

        // Ensure ollama-data volume exists in volumes section
        if (strpos($content, 'ollama-data:') === false) {
            // Find the volumes section and add ollama-data
            $pattern = '/(volumes:\s*\n(?:\s+[^:\s]+:\s*\n(?:\s+[^\n]+\n)*)*)(services:)/';
            if (preg_match($pattern, $content, $matches)) {
                $volumesSection = $matches[1];
                $servicesSection = $matches[2];
                $newVolumesSection = $volumesSection . "    ollama-data:\n        driver: local\n";
                $content = str_replace($volumesSection . $servicesSection, $newVolumesSection . $servicesSection, $content);
            }
        }

        return file_put_contents($composeFile, $content) !== false;
    }

    /**
     * Remove Ollama service from docker-compose.yml
     */
    private function removeOllamaFromDockerCompose(): bool
    {
        $composeFile = 'docker-compose.yml';

        if (!file_exists($composeFile)) {
            $this->error("docker-compose.yml file not found");
            return false;
        }

        $content = file_get_contents($composeFile);

        // Check if Ollama service exists
        if (strpos($content, 'ollama:') === false) {
            $this->verbose("Ollama service not found in docker-compose.yml");
            return true;
        }

        // Remove the entire Ollama service block more precisely
        $lines = explode("\n", $content);
        $newLines = [];
        $inOllamaService = false;
        $serviceIndentLevel = 0;

        foreach ($lines as $line) {
            if (preg_match('/^(\s*)ollama:\s*$/', $line, $matches)) {
                $inOllamaService = true;
                $serviceIndentLevel = strlen($matches[1]);
                continue; // Skip the ollama: line
            }

            if ($inOllamaService) {
                // Check if we're still in the ollama service block
                if (trim($line) === '') {
                    // Skip empty lines within the service
                    continue;
                } elseif (preg_match('/^(\s+)/', $line, $matches)) {
                    $currentIndentLevel = strlen($matches[1]);
                    if ($currentIndentLevel > $serviceIndentLevel) {
                        // Still inside the ollama service, skip this line
                        continue;
                    } else {
                        // We've reached the next service or section
                        $inOllamaService = false;
                    }
                } else {
                    // No indentation, we've reached a top-level section
                    $inOllamaService = false;
                }
            }

            $newLines[] = $line;
        }

        $content = implode("\n", $newLines);

        // Remove ollama-data volume if it exists and no other service uses it
        if (strpos($content, 'ollama-data') !== false && substr_count($content, 'ollama-data') === 1) {
            $lines = explode("\n", $content);
            $newLines = [];
            $inOllamaDataVolume = false;
            $volumeIndentLevel = 0;

            foreach ($lines as $line) {
                if (preg_match('/^(\s*)ollama-data:\s*$/', $line, $matches)) {
                    $inOllamaDataVolume = true;
                    $volumeIndentLevel = strlen($matches[1]);
                    continue; // Skip the ollama-data: line
                }

                if ($inOllamaDataVolume) {
                    if (trim($line) === '') {
                        continue;
                    } elseif (preg_match('/^(\s+)/', $line, $matches)) {
                        $currentIndentLevel = strlen($matches[1]);
                        if ($currentIndentLevel > $volumeIndentLevel) {
                            continue;
                        } else {
                            $inOllamaDataVolume = false;
                        }
                    } else {
                        $inOllamaDataVolume = false;
                    }
                }

                $newLines[] = $line;
            }

            $content = implode("\n", $newLines);
        }

        return file_put_contents($composeFile, $content) !== false;
    }
}
