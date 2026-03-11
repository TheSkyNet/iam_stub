<?php

namespace Tests\Unit\Service\Payment;

use PHPUnit\Framework\TestCase;
use IamLab\Service\Payment\Integrations\PaceIntegration;

class PaceIntegrationTest extends TestCase
{
    private array $config;
    private PaceIntegration $integration;

    protected function setUp(): void
    {
        $this->config = [
            'enabled' => true,
            'api_key' => 'test_api_key',
            'secret' => 'test_secret'
        ];
        $this->integration = new PaceIntegration($this->config);
    }

    public function testCreatePayment(): void
    {
        $paymentData = [
            'amount' => 100.00,
            'currency' => 'GBP'
        ];

        // Partial mock to avoid real network call
        $mock = $this->getMockBuilder(PaceIntegration::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['request'])
            ->getMock();

        $mock->method('request')->willReturn([
            'transactionID' => 'PACE-TEST-123',
            'status' => 'completed',
            'checkoutURL' => 'http://pace.test'
        ]);

        $result = $mock->createPayment($paymentData);

        $this->assertTrue($result['success']);
        $this->assertEquals('PACE-TEST-123', $result['transaction_id']);
        $this->assertEquals(100.00, $result['amount']);
        $this->assertEquals('GBP', $result['currency']);
    }

    public function testCreateSubscription(): void
    {
        $subscriptionData = [
            'plan_id' => 'premium-monthly'
        ];

        // Pace createSubscription is already somewhat simulated in the real class as a fallback
        $result = $this->integration->createSubscription($subscriptionData);

        $this->assertTrue($result['success']);
        $this->assertStringStartsWith('PACE-SUB-', $result['subscription_id']);
    }

    public function testHealthCheck(): void
    {
        $mock = $this->getMockBuilder(PaceIntegration::class)
            ->setConstructorArgs([$this->config])
            ->onlyMethods(['request'])
            ->getMock();

        $mock->method('request')->willReturn(['success' => true]);

        // Integration healthCheck checks for API key and connectivity
        $this->assertTrue($mock->healthCheck());

        $disabledConfig = ['enabled' => false, 'api_key' => ''];
        $disabledIntegration = new PaceIntegration($disabledConfig);
        $this->assertFalse($disabledIntegration->healthCheck());
    }

    public function testGetCapabilities(): void
    {
        $capabilities = $this->integration->getCapabilities();
        $this->assertArrayHasKey('single_payments', $capabilities);
        $this->assertTrue($capabilities['single_payments']);
    }
}
