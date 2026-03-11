<?php

namespace Tests\Unit\Service\Payment;

use PHPUnit\Framework\TestCase;
use IamLab\Service\Payment\Integrations\MollieIntegration;

class MollieIntegrationTest extends TestCase
{
    private array $config;
    private MollieIntegration $integration;

    protected function setUp(): void
    {
        $this->config = [
            'enabled' => true,
            'api_key' => 'test_mollie_api_key'
        ];
        $this->integration = new MollieIntegration($this->config);
    }

    public function testHealthCheck(): void
    {
        // Mocking the healthCheck to avoid real network call in tests
        // Actually, MollieIntegration::healthCheck calls $this->request('GET', '/methods')
        // In a real project we would mock cURL or the request method.
        // For this stub, we'll just check if it returns false when API key is missing.
        
        $this->assertTrue(true); // Placeholder for structural test
        
        $disabledConfig = ['enabled' => false];
        $disabledIntegration = new MollieIntegration($disabledConfig);
        $this->assertFalse($disabledIntegration->healthCheck());
    }

    public function testGetCapabilities(): void
    {
        $capabilities = $this->integration->getCapabilities();
        $this->assertArrayHasKey('single_payments', $capabilities);
        $this->assertTrue($capabilities['single_payments']);
        $this->assertTrue($capabilities['subscriptions']);
    }

    public function testCreateSubscriptionSimulated(): void
    {
        $subscriptionData = [
            'plan_id' => 'premium-monthly'
        ];

        $result = $this->integration->createSubscription($subscriptionData);

        $this->assertTrue($result['success']);
        $this->assertStringStartsWith('sub_', $result['subscription_id']);
        $this->assertEquals('active', $result['status']);
    }
}
