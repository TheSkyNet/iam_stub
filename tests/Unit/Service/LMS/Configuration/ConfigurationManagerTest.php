<?php

namespace Tests\Unit\Service\LMS\Configuration;

use PHPUnit\Framework\TestCase;
use IamLab\Service\LMS\Configuration\ConfigurationManager;

class ConfigurationManagerTest extends TestCase
{
    private ConfigurationManager $configManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->configManager = new ConfigurationManager();
    }

    public function testCanBeInstantiated(): void
    {
        $this->assertInstanceOf(ConfigurationManager::class, $this->configManager);
    }

    public function testGetAllConfigurations(): void
    {
        $configs = $this->configManager->getAllConfigurations();
        
        $this->assertIsArray($configs);
        $this->assertArrayHasKey('gemini', $configs);
        $this->assertArrayHasKey('ollama', $configs);
        $this->assertArrayHasKey('tencent_edu', $configs);
    }

    public function testGetIntegrationConfig(): void
    {
        $ollamaConfig = $this->configManager->getIntegrationConfig('ollama');
        
        $this->assertIsArray($ollamaConfig);
        $this->assertArrayHasKey('enabled', $ollamaConfig);
        $this->assertArrayHasKey('host', $ollamaConfig);
        $this->assertArrayHasKey('model', $ollamaConfig);
    }

    public function testIsIntegrationEnabled(): void
    {
        // Ollama should be enabled by default
        $this->assertTrue($this->configManager->isIntegrationEnabled('ollama'));
        
        // Gemini should be disabled by default (no API key)
        $this->assertFalse($this->configManager->isIntegrationEnabled('gemini'));
    }

    public function testGetEnabledIntegrations(): void
    {
        $enabled = $this->configManager->getEnabledIntegrations();
        
        $this->assertIsArray($enabled);
        $this->assertContains('ollama', $enabled);
    }

    public function testValidateConfiguration(): void
    {
        $errors = $this->configManager->validateConfiguration();
        
        $this->assertIsArray($errors);
        // Should have no errors for ollama (enabled by default)
        // May have errors for other integrations if they're enabled but missing credentials
    }

    public function testGetNonExistentIntegrationConfig(): void
    {
        $config = $this->configManager->getIntegrationConfig('nonexistent');
        
        $this->assertIsArray($config);
        $this->assertEmpty($config);
    }

    public function testIsNonExistentIntegrationEnabled(): void
    {
        $this->assertFalse($this->configManager->isIntegrationEnabled('nonexistent'));
    }
}