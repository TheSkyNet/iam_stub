<?php

namespace IamLab\Service\LMS\Registry;

use Exception;
use IamLab\Service\LMS\Integrations\LMSIntegrationInterface;
use IamLab\Service\LMS\Factory\IntegrationFactory;
use IamLab\Service\LMS\Configuration\ConfigurationManager;
use IamLab\Service\LMS\Exception\IntegrationNotFoundException;

/**
 * Integration Registry
 * 
 * Manages integration instances and their lifecycle
 * Following Single Responsibility Principle (SRP)
 */
class IntegrationRegistry
{
    private array $integrations = [];
    private array $healthStatus = [];
    private array $errors = [];
    private ConfigurationManager $configManager;

    public function __construct(ConfigurationManager $configManager)
    {
        $this->configManager = $configManager;
        $this->initializeIntegrations();
    }

    /**
     * Initialize all enabled integrations
     */
    private function initializeIntegrations(): void
    {
        $configurations = $this->configManager->getAllConfigurations();
        $result = IntegrationFactory::createFromConfig($configurations);
        
        $this->integrations = $result['integrations'];
        $this->errors = $result['errors'];
        
        // Initialize health status
        $this->refreshHealthStatus();
    }

    /**
     * Get integration instance
     * 
     * @throws IntegrationNotFoundException
     */
    public function getIntegration(string $name): LMSIntegrationInterface
    {
        if (!$this->hasIntegration($name)) {
            throw new IntegrationNotFoundException("Integration '{$name}' is not available");
        }

        return $this->integrations[$name];
    }

    /**
     * Check if integration exists and is available
     */
    public function hasIntegration(string $name): bool
    {
        return isset($this->integrations[$name]);
    }

    /**
     * Get all available integration names
     */
    public function getAvailableIntegrations(): array
    {
        return array_keys($this->integrations);
    }

    /**
     * Get all integration instances
     */
    public function getAllIntegrations(): array
    {
        return $this->integrations;
    }

    /**
     * Check if integration is healthy
     */
    public function isIntegrationHealthy(string $name): bool
    {
        return $this->healthStatus[$name] ?? false;
    }

    /**
     * Get health status for all integrations
     */
    public function getHealthStatus(): array
    {
        return $this->healthStatus;
    }

    /**
     * Refresh health status for all integrations
     */
    public function refreshHealthStatus(): void
    {
        foreach ($this->integrations as $name => $integration) {
            $this->healthStatus[$name] = $integration->healthCheck();
        }
    }

    /**
     * Refresh health status for specific integration
     */
    public function refreshIntegrationHealth(string $name): bool
    {
        if (!$this->hasIntegration($name)) {
            return false;
        }

        $this->healthStatus[$name] = $this->integrations[$name]->healthCheck();
        return $this->healthStatus[$name];
    }

    /**
     * Get integration status information
     */
    public function getIntegrationStatus(): array
    {
        $status = [];
        
        foreach ($this->integrations as $name => $integration) {
            $status[$name] = [
                'enabled' => true,
                'healthy' => $this->healthStatus[$name] ?? false,
                'config' => $this->configManager->getIntegrationConfig($name),
                'capabilities' => $integration->getCapabilities(),
                'class' => get_class($integration)
            ];
        }

        // Add information about failed integrations
        foreach ($this->errors as $name => $error) {
            $status[$name] = [
                'enabled' => $this->configManager->isIntegrationEnabled($name),
                'healthy' => false,
                'error' => $error,
                'config' => $this->configManager->getIntegrationConfig($name)
            ];
        }

        return $status;
    }

    /**
     * Get initialization errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Check if there are any initialization errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get error for specific integration
     */
    public function getIntegrationError(string $name): ?string
    {
        return $this->errors[$name] ?? null;
    }

    /**
     * Add integration dynamically
     */
    public function addIntegration(string $name, LMSIntegrationInterface $integration): void
    {
        $this->integrations[$name] = $integration;
        $this->healthStatus[$name] = $integration->healthCheck();
        
        // Remove from errors if it was there
        unset($this->errors[$name]);
    }

    /**
     * Remove integration
     */
    public function removeIntegration(string $name): void
    {
        unset($this->integrations[$name]);
        unset($this->healthStatus[$name]);
        unset($this->errors[$name]);
    }

    /**
     * Get integration capabilities
     */
    public function getIntegrationCapabilities(string $name): array
    {
        if (!$this->hasIntegration($name)) {
            return IntegrationFactory::getIntegrationCapabilities($name);
        }

        return $this->integrations[$name]->getCapabilities();
    }

    /**
     * Find integrations by capability
     */
    public function findIntegrationsByCapability(string $capability): array
    {
        $matches = [];
        
        foreach ($this->integrations as $name => $integration) {
            $capabilities = $integration->getCapabilities();
            if ($capabilities[$capability] ?? false) {
                $matches[] = $name;
            }
        }

        return $matches;
    }

    /**
     * Get best integration for a specific capability
     */
    public function getBestIntegrationFor(string $capability, array $preferences = []): ?string
    {
        $candidates = $this->findIntegrationsByCapability($capability);
        
        if (empty($candidates)) {
            return null;
        }

        // Filter by health status
        $healthyCandidates = array_filter($candidates, fn($name) => $this->isIntegrationHealthy($name));
        
        if (!empty($healthyCandidates)) {
            $candidates = $healthyCandidates;
        }

        // Apply preferences
        foreach ($preferences as $preferred) {
            if (in_array($preferred, $candidates)) {
                return $preferred;
            }
        }

        // Return first available
        return $candidates[0] ?? null;
    }

    /**
     * Execute operation with fallback integrations
     */
    public function executeWithFallback(callable $operation, array $integrationOrder = []): array
    {
        if (empty($integrationOrder)) {
            $integrationOrder = $this->getAvailableIntegrations();
        }

        $lastError = null;

        foreach ($integrationOrder as $integrationName) {
            if (!$this->hasIntegration($integrationName)) {
                continue;
            }

            try {
                $integration = $this->getIntegration($integrationName);
                $result = $operation($integration, $integrationName);
                
                if ($result['success'] ?? false) {
                    return $result;
                }
                
                $lastError = $result['error'] ?? 'Unknown error';
            } catch (Exception $e) {
                $lastError = $e->getMessage();
                continue;
            }
        }

        return [
            'success' => false,
            'error' => $lastError ?? 'No integrations available',
            'attempted_integrations' => $integrationOrder
        ];
    }

    /**
     * Get registry statistics
     */
    public function getStatistics(): array
    {
        $healthy = array_filter($this->healthStatus);
        $unhealthy = array_filter($this->healthStatus, fn($status) => !$status);
        
        return [
            'total_integrations' => count($this->integrations),
            'healthy_integrations' => count($healthy),
            'unhealthy_integrations' => count($unhealthy),
            'failed_initializations' => count($this->errors),
            'enabled_configurations' => count($this->configManager->getEnabledIntegrations()),
            'health_percentage' => count($this->integrations) > 0 
                ? round((count($healthy) / count($this->integrations)) * 100, 2) 
                : 0
        ];
    }
}