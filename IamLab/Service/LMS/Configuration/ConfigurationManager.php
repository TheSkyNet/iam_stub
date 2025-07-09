<?php

namespace IamLab\Service\LMS\Configuration;

/**
 * Configuration Manager for LMS Service
 * 
 * Handles all configuration loading and management responsibilities
 * Following Single Responsibility Principle (SRP)
 */
class ConfigurationManager
{
    private array $config = [];
    private ?object $configService = null;

    public function __construct(?object $configService = null)
    {
        $this->configService = $configService;
        $this->loadConfiguration();
    }

    /**
     * Load configuration from various sources
     */
    private function loadConfiguration(): void
    {
        $this->config = [
            'gemini' => $this->loadGeminiConfig(),
            'ollama' => $this->loadOllamaConfig(),
            'tencent_edu' => $this->loadTencentEduConfig()
        ];
    }

    /**
     * Get configuration for a specific integration
     */
    public function getIntegrationConfig(string $integration): array
    {
        return $this->config[$integration] ?? [];
    }

    /**
     * Get all configurations
     */
    public function getAllConfigurations(): array
    {
        return $this->config;
    }

    /**
     * Check if integration is enabled
     */
    public function isIntegrationEnabled(string $integration): bool
    {
        return $this->config[$integration]['enabled'] ?? false;
    }

    /**
     * Get list of enabled integrations
     */
    public function getEnabledIntegrations(): array
    {
        return array_keys(array_filter($this->config, fn($config) => $config['enabled'] ?? false));
    }

    /**
     * Load Gemini configuration
     */
    private function loadGeminiConfig(): array
    {
        return [
            'enabled' => $this->getConfigValue('lms.gemini.enabled', 'LMS_GEMINI_ENABLED', false, 'bool'),
            'api_key' => $this->getConfigValue('lms.gemini.api_key', 'LMS_GEMINI_API_KEY', ''),
            'model' => $this->getConfigValue('lms.gemini.model', 'LMS_GEMINI_MODEL', 'gemini-pro'),
        ];
    }

    /**
     * Load Ollama configuration
     */
    private function loadOllamaConfig(): array
    {
        return [
            'enabled' => $this->getConfigValue('lms.ollama.enabled', 'LMS_OLLAMA_ENABLED', true, 'bool'),
            'host' => $this->getConfigValue('lms.ollama.host', 'LMS_OLLAMA_HOST', 'http://ollama:11434'),
            'model' => $this->getConfigValue('lms.ollama.model', 'LMS_OLLAMA_MODEL', 'llama2'),
        ];
    }

    /**
     * Load Tencent Education configuration
     */
    private function loadTencentEduConfig(): array
    {
        return [
            'enabled' => $this->getConfigValue('lms.tencent_edu.enabled', 'LMS_TENCENT_EDU_ENABLED', false, 'bool'),
            'app_id' => $this->getConfigValue('lms.tencent_edu.app_id', 'LMS_TENCENT_EDU_APP_ID', ''),
            'secret_key' => $this->getConfigValue('lms.tencent_edu.secret_key', 'LMS_TENCENT_EDU_SECRET_KEY', ''),
            'region' => $this->getConfigValue('lms.tencent_edu.region', 'LMS_TENCENT_EDU_REGION', 'ap-beijing'),
        ];
    }

    /**
     * Get configuration value from service or environment
     */
    private function getConfigValue(string $serviceKey, string $envKey, mixed $default, string $type = 'string'): mixed
    {
        $value = $this->configService 
            ? $this->configService->get($serviceKey, $default)
            : ($_ENV[$envKey] ?? $default);

        return $this->castValue($value, $type);
    }

    /**
     * Cast value to appropriate type
     */
    private function castValue(mixed $value, string $type): mixed
    {
        return match($type) {
            'bool' => (bool)$value,
            'int' => (int)$value,
            'float' => (float)$value,
            'array' => is_array($value) ? $value : [],
            default => (string)$value
        };
    }

    /**
     * Validate configuration
     */
    public function validateConfiguration(): array
    {
        $errors = [];

        foreach ($this->config as $integration => $config) {
            if (!$config['enabled']) {
                continue;
            }

            $errors = array_merge($errors, $this->validateIntegrationConfig($integration, $config));
        }

        return $errors;
    }

    /**
     * Validate specific integration configuration
     */
    private function validateIntegrationConfig(string $integration, array $config): array
    {
        $errors = [];

        switch ($integration) {
            case 'gemini':
                if (empty($config['api_key'])) {
                    $errors[] = "Gemini integration is enabled but API key is missing";
                }
                break;

            case 'tencent_edu':
                if (empty($config['app_id']) || empty($config['secret_key'])) {
                    $errors[] = "Tencent Education integration is enabled but credentials are missing";
                }
                break;

            case 'ollama':
                if (empty($config['host'])) {
                    $errors[] = "Ollama integration is enabled but host is missing";
                }
                break;
        }

        return $errors;
    }
}