<?php

namespace IamLab\Service\Payment\Registry;

use Exception;
use IamLab\Service\Payment\Integrations\PaymentIntegrationInterface;
use IamLab\Service\Payment\Factory\IntegrationFactory;
use IamLab\Service\Payment\Configuration\ConfigurationManager;

/**
 * Payment Integration Registry
 * 
 * Manages payment integration instances
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
        
        $this->refreshHealthStatus();
    }

    /**
     * Get integration instance
     */
    public function getIntegration(string $name): PaymentIntegrationInterface
    {
        if (!$this->hasIntegration($name)) {
            throw new Exception("Payment provider '{$name}' is not available");
        }

        return $this->integrations[$name];
    }

    /**
     * Check if integration exists
     */
    public function hasIntegration(string $name): bool
    {
        return isset($this->integrations[$name]);
    }

    /**
     * Get all available provider names
     */
    public function getAvailableProviders(): array
    {
        return array_keys($this->integrations);
    }

    /**
     * Refresh health status
     */
    public function refreshHealthStatus(): void
    {
        foreach ($this->integrations as $name => $integration) {
            $this->healthStatus[$name] = $integration->healthCheck();
        }
    }

    /**
     * Get health status for all integrations
     */
    public function getHealthStatus(): array
    {
        return $this->healthStatus;
    }
}
