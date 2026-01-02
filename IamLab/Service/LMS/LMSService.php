<?php

namespace IamLab\Service\LMS;

use Exception;
use IamLab\Service\LMS\Configuration\ConfigurationManager;
use IamLab\Service\LMS\Registry\IntegrationRegistry;
use IamLab\Service\LMS\Exception\IntegrationNotFoundException;
use IamLab\Service\LMS\Exception\LMSException;
use Phalcon\Di\Injectable;

// Conditionally use Phalcon Injectable if available
if (class_exists('Phalcon\Di\Injectable')) {
    class LMSServiceBase extends Injectable {}
} else {
    class LMSServiceBase {}
}

/**
 * LMS Integration Service
 * 
 * This service provides a unified interface for integrating with various
 * Learning Management Systems and AI platforms including:
 * - Google Gemini API
 * - Ollama (local LLM)
 * - Tencent Education Cloud (Chinese LMS)
 * 
 * Refactored to follow SOLID principles:
 * - Single Responsibility: Each class has one clear responsibility
 * - Open/Closed: Easy to extend with new integrations
 * - Liskov Substitution: All integrations implement the same interface
 * - Interface Segregation: Clean, focused interfaces
 * - Dependency Inversion: Depends on abstractions, not concretions
 */
class LMSService extends LMSServiceBase
{
    private ConfigurationManager $configManager;
    private IntegrationRegistry $registry;
    private bool $initialized = false;

    public function initialize(): void
    {
        if ($this->initialized) {
            return;
        }

        $this->initializeComponents();
        $this->initialized = true;
    }

    /**
     * Initialize service components
     */
    private function initializeComponents(): void
    {
        // Get configuration service from DI container if available
        $configService = null;

        // Avoid calling $this->getDI() directly because it throws when no DI is set
        try {
            if (class_exists('Phalcon\\Di\\Di')) {
                $di = \Phalcon\Di\Di::getDefault();
                if ($di && $di->has('config')) {
                    $configService = $di->get('config');
                }
            }
        } catch (\Throwable $e) {
            // No available DI. That's fine for tests/CLI: ConfigurationManager will fall back to env.
        }

        // Initialize configuration manager
        $this->configManager = new ConfigurationManager($configService);

        // Initialize integration registry
        $this->registry = new IntegrationRegistry($this->configManager);
    }

    /**
     * Get configuration manager instance
     */
    public function getConfigurationManager(): ConfigurationManager
    {
        $this->ensureInitialized();
        return $this->configManager;
    }

    /**
     * Get integration registry instance
     */
    public function getIntegrationRegistry(): IntegrationRegistry
    {
        $this->ensureInitialized();
        return $this->registry;
    }

    /**
     * Ensure service is initialized
     */
    private function ensureInitialized(): void
    {
        if (!$this->initialized) {
            $this->initialize();
        }
    }

    /**
     * Generate content using specified LMS integration
     */
    public function generateContent(string $prompt, string $integration = 'ollama', array $options = []): array
    {
        $this->ensureInitialized();

        try {
            $integrationInstance = $this->registry->getIntegration($integration);
            $result = $integrationInstance->generateContent($prompt, $options);

            // Add metadata to result
            $result['integration'] = $integration;
            $result['timestamp'] = time();

            return $result;
        } catch (IntegrationNotFoundException $e) {
            return $this->formatErrorResponse($e, $integration, 'content_generation');
        } catch (Exception $e) {
            return $this->formatErrorResponse($e, $integration, 'content_generation');
        }
    }

    /**
     * Create a course using specified LMS integration
     */
    public function createCourse(array $courseData, string $integration = 'tencent_edu'): array
    {
        $this->ensureInitialized();

        try {
            $integrationInstance = $this->registry->getIntegration($integration);
            $result = $integrationInstance->createCourse($courseData);

            // Add metadata to result
            $result['integration'] = $integration;
            $result['timestamp'] = time();

            return $result;
        } catch (IntegrationNotFoundException $e) {
            return $this->formatErrorResponse($e, $integration, 'course_creation');
        } catch (Exception $e) {
            return $this->formatErrorResponse($e, $integration, 'course_creation');
        }
    }

    /**
     * Analyze text using AI integrations
     */
    public function analyzeText(string $text, string $integration = 'gemini', array $options = []): array
    {
        $this->ensureInitialized();

        try {
            $integrationInstance = $this->registry->getIntegration($integration);
            $result = $integrationInstance->analyzeText($text, $options);

            // Add metadata to result
            $result['integration'] = $integration;
            $result['timestamp'] = time();

            return $result;
        } catch (IntegrationNotFoundException $e) {
            return $this->formatErrorResponse($e, $integration, 'text_analysis');
        } catch (Exception $e) {
            return $this->formatErrorResponse($e, $integration, 'text_analysis');
        }
    }

    /**
     * Get available integrations
     */
    public function getAvailableIntegrations(): array
    {
        $this->ensureInitialized();
        return $this->registry->getAvailableIntegrations();
    }

    /**
     * Check if integration is available
     */
    public function isIntegrationAvailable(string $integration): bool
    {
        $this->ensureInitialized();
        return $this->registry->hasIntegration($integration);
    }

    /**
     * Get integration status
     */
    public function getIntegrationStatus(): array
    {
        $this->ensureInitialized();
        return $this->registry->getIntegrationStatus();
    }

    /**
     * Generate content with automatic fallback to healthy integrations
     */
    public function generateContentWithFallback(string $prompt, array $integrationOrder = [], array $options = []): array
    {
        $this->ensureInitialized();

        if (empty($integrationOrder)) {
            // Default fallback order: local first, then cloud services
            $integrationOrder = ['ollama', 'gemini', 'tencent_edu'];
        }

        return $this->registry->executeWithFallback(
            function($integration, $integrationName) use ($prompt, $options) {
                $result = $integration->generateContent($prompt, $options);
                $result['integration'] = $integrationName;
                $result['timestamp'] = time();
                return $result;
            },
            $integrationOrder
        );
    }

    /**
     * Get best integration for a specific capability
     */
    public function getBestIntegrationFor(string $capability, array $preferences = []): ?string
    {
        $this->ensureInitialized();
        return $this->registry->getBestIntegrationFor($capability, $preferences);
    }

    /**
     * Get integration capabilities
     */
    public function getIntegrationCapabilities(string $integration): array
    {
        $this->ensureInitialized();
        return $this->registry->getIntegrationCapabilities($integration);
    }

    /**
     * Get service statistics
     */
    public function getStatistics(): array
    {
        $this->ensureInitialized();
        return $this->registry->getStatistics();
    }

    /**
     * Refresh health status for all integrations
     */
    public function refreshHealthStatus(): void
    {
        $this->ensureInitialized();
        $this->registry->refreshHealthStatus();
    }

    /**
     * Format error response consistently
     */
    private function formatErrorResponse(Exception $e, string $integration, string $operation): array
    {
        $response = [
            'success' => false,
            'error' => $e->getMessage(),
            'integration' => $integration,
            'operation' => $operation,
            'timestamp' => time()
        ];

        // Add additional context for LMS exceptions
        if ($e instanceof LMSException) {
            $response['context'] = $e->getContext();
            $response['error_code'] = $e->getCode();
        }

        return $response;
    }
}
