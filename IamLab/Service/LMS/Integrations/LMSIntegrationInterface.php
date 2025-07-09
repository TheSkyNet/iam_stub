<?php

namespace IamLab\Service\LMS\Integrations;

/**
 * LMS Integration Interface
 * 
 * Defines the contract that all LMS integrations must implement
 */
interface LMSIntegrationInterface
{
    /**
     * Initialize the integration with configuration
     */
    public function __construct(array $config);

    /**
     * Generate content using the LMS/AI service
     */
    public function generateContent(string $prompt, array $options = []): array;

    /**
     * Create a course in the LMS
     */
    public function createCourse(array $courseData): array;

    /**
     * Analyze text content
     */
    public function analyzeText(string $text, array $options = []): array;

    /**
     * Check if the integration is healthy and accessible
     */
    public function healthCheck(): bool;

    /**
     * Get integration-specific capabilities
     */
    public function getCapabilities(): array;
}