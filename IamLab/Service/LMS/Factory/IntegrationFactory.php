<?php

namespace IamLab\Service\LMS\Factory;

use IamLab\Service\LMS\Integrations\LMSIntegrationInterface;
use IamLab\Service\LMS\Integrations\GeminiIntegration;
use IamLab\Service\LMS\Integrations\OllamaIntegration;
use IamLab\Service\LMS\Integrations\TencentEduIntegration;
use IamLab\Service\LMS\Exception\IntegrationNotFoundException;
use IamLab\Service\LMS\Exception\InvalidConfigurationException;

/**
 * Integration Factory
 * 
 * Handles creation of integration instances
 * Following Open/Closed Principle (OCP) and Dependency Inversion Principle (DIP)
 */
class IntegrationFactory
{
    /**
     * Registry of available integration classes
     */
    private static array $integrationClasses = [
        'gemini' => GeminiIntegration::class,
        'ollama' => OllamaIntegration::class,
        'tencent_edu' => TencentEduIntegration::class,
    ];

    /**
     * Create an integration instance
     * 
     * @throws IntegrationNotFoundException
     * @throws InvalidConfigurationException
     */
    public static function create(string $integration, array $config): LMSIntegrationInterface
    {
        if (!self::isSupported($integration)) {
            throw new IntegrationNotFoundException("Integration '{$integration}' is not supported");
        }

        $className = self::$integrationClasses[$integration];
        
        try {
            return new $className($config);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidConfigurationException(
                "Invalid configuration for integration '{$integration}': " . $e->getMessage(),
                0,
                $e
            );
        } catch (\Exception $e) {
            throw new InvalidConfigurationException(
                "Failed to create integration '{$integration}': " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Check if integration is supported
     */
    public static function isSupported(string $integration): bool
    {
        return isset(self::$integrationClasses[$integration]);
    }

    /**
     * Get list of supported integrations
     */
    public static function getSupportedIntegrations(): array
    {
        return array_keys(self::$integrationClasses);
    }

    /**
     * Register a new integration class
     * 
     * This allows extending the factory without modifying existing code (OCP)
     */
    public static function register(string $name, string $className): void
    {
        if (!is_subclass_of($className, LMSIntegrationInterface::class)) {
            throw new \InvalidArgumentException(
                "Class '{$className}' must implement LMSIntegrationInterface"
            );
        }

        self::$integrationClasses[$name] = $className;
    }

    /**
     * Unregister an integration class
     */
    public static function unregister(string $name): void
    {
        unset(self::$integrationClasses[$name]);
    }

    /**
     * Get integration class name
     */
    public static function getIntegrationClass(string $integration): ?string
    {
        return self::$integrationClasses[$integration] ?? null;
    }

    /**
     * Create multiple integrations from configuration
     */
    public static function createFromConfig(array $configurations): array
    {
        $integrations = [];
        $errors = [];

        foreach ($configurations as $name => $config) {
            if (!($config['enabled'] ?? false)) {
                continue;
            }

            try {
                $integrations[$name] = self::create($name, $config);
            } catch (IntegrationNotFoundException | InvalidConfigurationException $e) {
                $errors[$name] = $e->getMessage();
            }
        }

        return [
            'integrations' => $integrations,
            'errors' => $errors
        ];
    }

    /**
     * Validate integration configuration without creating instance
     */
    public static function validateConfig(string $integration, array $config): array
    {
        $errors = [];

        if (!self::isSupported($integration)) {
            $errors[] = "Integration '{$integration}' is not supported";
            return $errors;
        }

        // Basic validation - each integration can have its own validation rules
        switch ($integration) {
            case 'gemini':
                if (empty($config['api_key'])) {
                    $errors[] = "API key is required for Gemini integration";
                }
                break;

            case 'tencent_edu':
                if (empty($config['app_id'])) {
                    $errors[] = "App ID is required for Tencent Education integration";
                }
                if (empty($config['secret_key'])) {
                    $errors[] = "Secret key is required for Tencent Education integration";
                }
                break;

            case 'ollama':
                if (empty($config['host'])) {
                    $errors[] = "Host is required for Ollama integration";
                }
                break;
        }

        return $errors;
    }

    /**
     * Get integration capabilities without creating instance
     */
    public static function getIntegrationCapabilities(string $integration): array
    {
        if (!self::isSupported($integration)) {
            return [];
        }

        // Return static capabilities for each integration
        return match($integration) {
            'gemini' => [
                'content_generation' => true,
                'text_analysis' => true,
                'course_creation' => true,
                'real_time' => true,
                'languages' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh'],
                'max_tokens' => 8192,
                'supports_images' => false,
                'supports_code' => true
            ],
            'ollama' => [
                'content_generation' => true,
                'text_analysis' => true,
                'course_creation' => true,
                'real_time' => true,
                'local_processing' => true,
                'privacy_focused' => true,
                'offline_capable' => true,
                'languages' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar'],
                'supports_code' => true,
                'supports_reasoning' => true,
                'customizable' => true
            ],
            'tencent_edu' => [
                'content_generation' => true,
                'text_analysis' => true,
                'course_creation' => true,
                'real_time' => true,
                'video_conferencing' => true,
                'screen_sharing' => true,
                'whiteboard' => true,
                'recording' => true,
                'user_management' => true,
                'analytics' => true,
                'languages' => ['zh', 'en'],
                'regions' => ['ap-beijing', 'ap-shanghai', 'ap-guangzhou', 'ap-chengdu'],
                'max_participants' => 1000,
                'supports_mobile' => true
            ],
            default => []
        };
    }
}