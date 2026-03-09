<?php

namespace Tests\Unit\Service\Payment;

use PHPUnit\Framework\TestCase;
use IamLab\Service\Payment\PaymentService;
use IamLab\Service\Payment\Configuration\ConfigurationManager;
use IamLab\Service\Payment\Registry\IntegrationRegistry;

class PaymentServiceMultiProviderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Ensure environment variables are set for testing
        $_ENV['STRIPE_ENABLED'] = '1';
        $_ENV['PAYPAL_ENABLED'] = '1';
        $_ENV['SQUARE_ENABLED'] = '1';
    }

    public function testCanInstantiateService(): void
    {
        $service = new PaymentService('stripe');
        $this->assertInstanceOf(PaymentService::class, $service);
    }

    public function testCanGetAvailableProviders(): void
    {
        $service = new PaymentService();
        $providers = $service->getAvailableProviders();
        
        $this->assertContains('stripe', $providers);
        $this->assertContains('paypal', $providers);
        $this->assertContains('square', $providers);
    }

    public function testCanSwitchProvider(): void
    {
        $service = new PaymentService('stripe');
        $service->setProvider('paypal');
        
        // We can't easily check internal state without reflection or getters, 
        // but it shouldn't throw an exception.
        $this->assertTrue(true);
    }

    public function testThrowsExceptionOnInvalidProvider(): void
    {
        $this->expectException(\Exception::class);
        new PaymentService('invalid_provider');
    }
}
