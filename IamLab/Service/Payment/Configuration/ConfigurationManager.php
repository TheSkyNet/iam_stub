<?php

namespace IamLab\Service\Payment\Configuration;

use function App\Core\Helpers\env;

/**
 * Configuration Manager for Payment Services
 * 
 * Manages configuration for different payment providers
 */
class ConfigurationManager
{
    /**
     * Get configuration for a specific integration
     */
    public function getIntegrationConfig(string $name): array
    {
        $prefix = strtoupper($name);
        
        switch ($name) {
            case 'stripe':
                return [
                    'enabled' => (bool)env('STRIPE_ENABLED', true),
                    'api_key' => env('STRIPE_API_KEY', ''),
                    'public_key' => env('STRIPE_PUBLIC_KEY', ''),
                    'webhook_secret' => env('STRIPE_WEBHOOK_SECRET', '')
                ];
            case 'paypal':
                return [
                    'enabled' => (bool)env('PAYPAL_ENABLED', true),
                    'client_id' => env('PAYPAL_CLIENT_ID', ''),
                    'client_secret' => env('PAYPAL_CLIENT_SECRET', ''),
                    'mode' => env('PAYPAL_MODE', 'sandbox')
                ];
            case 'square':
                return [
                    'enabled' => (bool)env('SQUARE_ENABLED', true),
                    'access_token' => env('SQUARE_ACCESS_TOKEN', ''),
                    'application_id' => env('SQUARE_APPLICATION_ID', ''),
                    'location_id' => env('SQUARE_LOCATION_ID', '')
                ];
            case 'pace':
                return [
                    'enabled' => (bool)env('PACE_ENABLED', true),
                    'api_key' => env('PACE_API_KEY', ''),
                    'secret' => env('PACE_SECRET', '')
                ];
            case 'mollie':
                return [
                    'enabled' => (bool)env('MOLLIE_ENABLED', true),
                    'api_key' => env('MOLLIE_API_KEY', '')
                ];
            case 'revolut':
                return [
                    'enabled' => (bool)env('REVOLUT_ENABLED', true),
                    'api_key' => env('REVOLUT_API_KEY', ''),
                    'mode' => env('REVOLUT_MODE', 'sandbox')
                ];
            default:
                return [
                    'enabled' => (bool)env("PAYMENT_{$prefix}_ENABLED", false)
                ];
        }
    }

    /**
     * Check if an integration is enabled
     */
    public function isIntegrationEnabled(string $name): bool
    {
        $config = $this->getIntegrationConfig($name);
        return $config['enabled'] ?? false;
    }

    /**
     * Get all enabled integrations
     */
    public function getEnabledIntegrations(): array
    {
        $all = ['stripe', 'paypal', 'square', 'pace', 'mollie', 'revolut'];
        return array_filter($all, fn($name) => $this->isIntegrationEnabled($name));
    }

    /**
     * Get all integration configurations
     */
    public function getAllConfigurations(): array
    {
        $configs = [];
        foreach (['stripe', 'paypal', 'square', 'pace', 'mollie', 'revolut'] as $name) {
            $configs[$name] = $this->getIntegrationConfig($name);
        }
        return $configs;
    }
}
