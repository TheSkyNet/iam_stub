<?php

namespace Tests\Unit\Service\LMS;

use PHPUnit\Framework\TestCase;
use IamLab\Service\LMS\LMSService;
use IamLab\Service\LMS\Configuration\ConfigurationManager;
use IamLab\Service\LMS\Registry\IntegrationRegistry;

class LMSServiceTest extends TestCase
{
    private LMSService $lmsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->lmsService = new LMSService();
        $this->lmsService->initialize();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(LMSService::class, $this->lmsService);
    }

    public function testInitialize(): void
    {
        $service = new LMSService();
        $service->initialize();
        
        // Should not throw any exceptions
        $this->assertInstanceOf(LMSService::class, $service);
    }

    public function testGetConfigurationManager(): void
    {
        $configManager = $this->lmsService->getConfigurationManager();
        
        $this->assertInstanceOf(ConfigurationManager::class, $configManager);
    }

    public function testGetIntegrationRegistry(): void
    {
        $registry = $this->lmsService->getIntegrationRegistry();
        
        $this->assertInstanceOf(IntegrationRegistry::class, $registry);
    }

    public function testGetAvailableIntegrations(): void
    {
        $integrations = $this->lmsService->getAvailableIntegrations();
        
        $this->assertIsArray($integrations);
        // Ollama should be available by default
        $this->assertContains('ollama', $integrations);
    }

    public function testIsIntegrationAvailable(): void
    {
        // Ollama should be available
        $this->assertTrue($this->lmsService->isIntegrationAvailable('ollama'));
        
        // Non-existent integration should not be available
        $this->assertFalse($this->lmsService->isIntegrationAvailable('nonexistent'));
    }

    public function testGetIntegrationStatus(): void
    {
        $status = $this->lmsService->getIntegrationStatus();
        
        $this->assertIsArray($status);
        $this->assertArrayHasKey('ollama', $status);
        
        // Check status structure
        $ollamaStatus = $status['ollama'];
        $this->assertArrayHasKey('enabled', $ollamaStatus);
        $this->assertArrayHasKey('healthy', $ollamaStatus);
        $this->assertArrayHasKey('config', $ollamaStatus);
    }

    public function testGenerateContentWithValidIntegration(): void
    {
        $result = $this->lmsService->generateContent(
            "Test prompt",
            'ollama',
            ['max_tokens' => 50]
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals('ollama', $result['integration']);
        
        // Note: The actual success depends on Ollama being available
        // In a real test environment, we might mock this
    }

    public function testGenerateContentWithInvalidIntegration(): void
    {
        $result = $this->lmsService->generateContent(
            "Test prompt",
            'nonexistent'
        );
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('operation', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals('nonexistent', $result['integration']);
        $this->assertEquals('content_generation', $result['operation']);
    }

    public function testCreateCourseWithValidIntegration(): void
    {
        $courseData = [
            'title' => 'Test Course',
            'description' => 'Test Description',
            'teacher_id' => 'test_teacher'
        ];
        
        $result = $this->lmsService->createCourse($courseData, 'ollama');
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals('ollama', $result['integration']);
    }

    public function testCreateCourseWithInvalidIntegration(): void
    {
        $courseData = [
            'title' => 'Test Course'
        ];
        
        $result = $this->lmsService->createCourse($courseData, 'nonexistent');
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('operation', $result);
        $this->assertEquals('nonexistent', $result['integration']);
        $this->assertEquals('course_creation', $result['operation']);
    }

    public function testAnalyzeTextWithValidIntegration(): void
    {
        $result = $this->lmsService->analyzeText(
            "This is test text to analyze",
            'ollama',
            ['type' => 'general']
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('timestamp', $result);
        $this->assertEquals('ollama', $result['integration']);
    }

    public function testAnalyzeTextWithInvalidIntegration(): void
    {
        $result = $this->lmsService->analyzeText(
            "Test text",
            'nonexistent'
        );
        
        $this->assertIsArray($result);
        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertArrayHasKey('integration', $result);
        $this->assertArrayHasKey('operation', $result);
        $this->assertEquals('nonexistent', $result['integration']);
        $this->assertEquals('text_analysis', $result['operation']);
    }

    public function testGenerateContentWithFallback(): void
    {
        $result = $this->lmsService->generateContentWithFallback(
            "Test prompt",
            ['nonexistent', 'ollama'],
            ['max_tokens' => 50]
        );
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('success', $result);
        
        // Should fallback to ollama if nonexistent fails
        if ($result['success']) {
            $this->assertArrayHasKey('integration', $result);
            $this->assertEquals('ollama', $result['integration']);
        }
    }

    public function testGetBestIntegrationFor(): void
    {
        $best = $this->lmsService->getBestIntegrationFor('content_generation', ['ollama']);
        
        // Should return ollama if it's available and supports content generation
        if ($this->lmsService->isIntegrationAvailable('ollama')) {
            $this->assertEquals('ollama', $best);
        }
    }

    public function testGetIntegrationCapabilities(): void
    {
        $capabilities = $this->lmsService->getIntegrationCapabilities('ollama');
        
        $this->assertIsArray($capabilities);
        $this->assertArrayHasKey('content_generation', $capabilities);
        $this->assertArrayHasKey('text_analysis', $capabilities);
        $this->assertArrayHasKey('course_creation', $capabilities);
    }

    public function testGetStatistics(): void
    {
        $stats = $this->lmsService->getStatistics();
        
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total_integrations', $stats);
        $this->assertArrayHasKey('healthy_integrations', $stats);
        $this->assertArrayHasKey('unhealthy_integrations', $stats);
        $this->assertArrayHasKey('health_percentage', $stats);
        
        $this->assertIsInt($stats['total_integrations']);
        $this->assertIsInt($stats['healthy_integrations']);
        $this->assertIsInt($stats['unhealthy_integrations']);
        $this->assertIsFloat($stats['health_percentage']);
    }

    public function testRefreshHealthStatus(): void
    {
        // Should not throw any exceptions
        $this->lmsService->refreshHealthStatus();
        
        // Verify that status is still accessible after refresh
        $status = $this->lmsService->getIntegrationStatus();
        $this->assertIsArray($status);
    }

    public function testMultipleInitializationCallsAreSafe(): void
    {
        $service = new LMSService();
        $service->initialize();
        $service->initialize(); // Second call should be safe
        
        $this->assertInstanceOf(LMSService::class, $service);
        $integrations1 = $service->getAvailableIntegrations();
        
        $service->initialize(); // Third call should also be safe
        $integrations2 = $service->getAvailableIntegrations();
        
        $this->assertEquals($integrations1, $integrations2);
    }
}