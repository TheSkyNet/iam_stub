<?php

namespace Tests\Unit\Service\LMS\Factory;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use IamLab\Service\LMS\Factory\IntegrationFactory;
use IamLab\Service\LMS\Integrations\LMSIntegrationInterface;
use IamLab\Service\LMS\Integrations\OllamaIntegration;
use IamLab\Service\LMS\Integrations\GeminiIntegration;
use IamLab\Service\LMS\Integrations\TencentEduIntegration;
use IamLab\Service\LMS\Exception\IntegrationNotFoundException;
use IamLab\Service\LMS\Exception\InvalidConfigurationException;
use stdClass;

class IntegrationFactoryTest extends TestCase
{
    public function testGetSupportedIntegrations(): void
    {
        $supported = IntegrationFactory::getSupportedIntegrations();
        
        $this->assertIsArray($supported);
        $this->assertContains('ollama', $supported);
        $this->assertContains('gemini', $supported);
        $this->assertContains('tencent_edu', $supported);
    }

    public function testIsSupported(): void
    {
        $this->assertTrue(IntegrationFactory::isSupported('ollama'));
        $this->assertTrue(IntegrationFactory::isSupported('gemini'));
        $this->assertTrue(IntegrationFactory::isSupported('tencent_edu'));
        $this->assertFalse(IntegrationFactory::isSupported('nonexistent'));
    }

    public function testCreateOllamaIntegration(): void
    {
        $config = [
            'host' => 'http://localhost:11434',
            'model' => 'llama2'
        ];
        
        $integration = IntegrationFactory::create('ollama', $config);
        
        $this->assertInstanceOf(LMSIntegrationInterface::class, $integration);
        $this->assertInstanceOf(OllamaIntegration::class, $integration);
    }

    public function testCreateGeminiIntegration(): void
    {
        $config = [
            'api_key' => 'test_api_key',
            'model' => 'gemini-pro'
        ];
        
        $integration = IntegrationFactory::create('gemini', $config);
        
        $this->assertInstanceOf(LMSIntegrationInterface::class, $integration);
        $this->assertInstanceOf(GeminiIntegration::class, $integration);
    }

    public function testCreateTencentEduIntegration(): void
    {
        $config = [
            'app_id' => 'test_app_id',
            'secret_key' => 'test_secret_key',
            'region' => 'ap-beijing'
        ];
        
        $integration = IntegrationFactory::create('tencent_edu', $config);
        
        $this->assertInstanceOf(LMSIntegrationInterface::class, $integration);
        $this->assertInstanceOf(TencentEduIntegration::class, $integration);
    }

    public function testCreateUnsupportedIntegrationThrowsException(): void
    {
        $this->expectException(IntegrationNotFoundException::class);
        $this->expectExceptionMessage("Integration 'nonexistent' is not supported");
        
        IntegrationFactory::create('nonexistent', []);
    }

    public function testCreateWithInvalidConfigurationThrowsException(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        
        // Gemini requires API key
        IntegrationFactory::create('gemini', []);
    }

    public function testGetIntegrationClass(): void
    {
        $this->assertEquals(OllamaIntegration::class, IntegrationFactory::getIntegrationClass('ollama'));
        $this->assertEquals(GeminiIntegration::class, IntegrationFactory::getIntegrationClass('gemini'));
        $this->assertEquals(TencentEduIntegration::class, IntegrationFactory::getIntegrationClass('tencent_edu'));
        $this->assertNull(IntegrationFactory::getIntegrationClass('nonexistent'));
    }

    public function testCreateFromConfig(): void
    {
        $configurations = [
            'ollama' => [
                'enabled' => true,
                'host' => 'http://localhost:11434',
                'model' => 'llama2'
            ],
            'gemini' => [
                'enabled' => true,
                'api_key' => 'test_api_key',
                'model' => 'gemini-pro'
            ],
            'tencent_edu' => [
                'enabled' => false, // Disabled
                'app_id' => '',
                'secret_key' => ''
            ],
            'invalid' => [
                'enabled' => true // This should cause an error
            ]
        ];
        
        $result = IntegrationFactory::createFromConfig($configurations);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('integrations', $result);
        $this->assertArrayHasKey('errors', $result);
        
        // Should have created ollama and gemini
        $this->assertArrayHasKey('ollama', $result['integrations']);
        $this->assertArrayHasKey('gemini', $result['integrations']);
        
        // Should not have created tencent_edu (disabled) or invalid (unsupported)
        $this->assertArrayNotHasKey('tencent_edu', $result['integrations']);
        $this->assertArrayNotHasKey('invalid', $result['integrations']);
        
        // Should have error for invalid integration
        $this->assertArrayHasKey('invalid', $result['errors']);
    }

    public function testValidateConfig(): void
    {
        // Valid Ollama config
        $errors = IntegrationFactory::validateConfig('ollama', [
            'host' => 'http://localhost:11434'
        ]);
        $this->assertEmpty($errors);
        
        // Invalid Ollama config (missing host)
        $errors = IntegrationFactory::validateConfig('ollama', []);
        $this->assertNotEmpty($errors);
        $this->assertContains('Host is required for Ollama integration', $errors);
        
        // Valid Gemini config
        $errors = IntegrationFactory::validateConfig('gemini', [
            'api_key' => 'test_key'
        ]);
        $this->assertEmpty($errors);
        
        // Invalid Gemini config (missing API key)
        $errors = IntegrationFactory::validateConfig('gemini', []);
        $this->assertNotEmpty($errors);
        $this->assertContains('API key is required for Gemini integration', $errors);
        
        // Valid Tencent config
        $errors = IntegrationFactory::validateConfig('tencent_edu', [
            'app_id' => 'test_app_id',
            'secret_key' => 'test_secret_key'
        ]);
        $this->assertEmpty($errors);
        
        // Invalid Tencent config (missing credentials)
        $errors = IntegrationFactory::validateConfig('tencent_edu', []);
        $this->assertNotEmpty($errors);
        $this->assertContains('App ID is required for Tencent Education integration', $errors);
        $this->assertContains('Secret key is required for Tencent Education integration', $errors);
        
        // Unsupported integration
        $errors = IntegrationFactory::validateConfig('nonexistent', []);
        $this->assertNotEmpty($errors);
        $this->assertContains("Integration 'nonexistent' is not supported", $errors);
    }

    public function testGetIntegrationCapabilities(): void
    {
        // Test Ollama capabilities
        $capabilities = IntegrationFactory::getIntegrationCapabilities('ollama');
        $this->assertIsArray($capabilities);
        $this->assertArrayHasKey('content_generation', $capabilities);
        $this->assertArrayHasKey('text_analysis', $capabilities);
        $this->assertArrayHasKey('local_processing', $capabilities);
        $this->assertTrue($capabilities['content_generation']);
        $this->assertTrue($capabilities['local_processing']);
        
        // Test Gemini capabilities
        $capabilities = IntegrationFactory::getIntegrationCapabilities('gemini');
        $this->assertIsArray($capabilities);
        $this->assertArrayHasKey('content_generation', $capabilities);
        $this->assertArrayHasKey('text_analysis', $capabilities);
        $this->assertArrayHasKey('max_tokens', $capabilities);
        $this->assertTrue($capabilities['content_generation']);
        $this->assertEquals(8192, $capabilities['max_tokens']);
        
        // Test Tencent capabilities
        $capabilities = IntegrationFactory::getIntegrationCapabilities('tencent_edu');
        $this->assertIsArray($capabilities);
        $this->assertArrayHasKey('course_creation', $capabilities);
        $this->assertArrayHasKey('video_conferencing', $capabilities);
        $this->assertArrayHasKey('max_participants', $capabilities);
        $this->assertTrue($capabilities['course_creation']);
        $this->assertTrue($capabilities['video_conferencing']);
        $this->assertEquals(1000, $capabilities['max_participants']);
        
        // Test unsupported integration
        $capabilities = IntegrationFactory::getIntegrationCapabilities('nonexistent');
        $this->assertIsArray($capabilities);
        $this->assertEmpty($capabilities);
    }

    public function testRegisterAndUnregisterIntegration(): void
    {
        // Create a mock integration class
        $mockClass = new class([]) implements LMSIntegrationInterface {
            public function __construct(array $config) {}
            public function generateContent(string $prompt, array $options = []): array { return []; }
            public function createCourse(array $courseData): array { return []; }
            public function analyzeText(string $text, array $options = []): array { return []; }
            public function healthCheck(): bool { return true; }
            public function getCapabilities(): array { return []; }
        };
        
        $className = get_class($mockClass);
        
        // Register new integration
        IntegrationFactory::register('test_integration', $className);
        
        $this->assertTrue(IntegrationFactory::isSupported('test_integration'));
        $this->assertEquals($className, IntegrationFactory::getIntegrationClass('test_integration'));
        
        // Unregister integration
        IntegrationFactory::unregister('test_integration');
        
        $this->assertFalse(IntegrationFactory::isSupported('test_integration'));
        $this->assertNull(IntegrationFactory::getIntegrationClass('test_integration'));
    }

    public function testRegisterInvalidClassThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Class 'stdClass' must implement LMSIntegrationInterface");
        
        IntegrationFactory::register('invalid', stdClass::class);
    }
}