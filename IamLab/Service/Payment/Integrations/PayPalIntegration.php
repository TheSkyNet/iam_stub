<?php

namespace IamLab\Service\Payment\Integrations;

/**
 * PayPal Integration (Mock)
 * 
 * Implements a mock PayPal integration for the payment system
 */
class PayPalIntegration implements PaymentIntegrationInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create a single payment
     */
    public function createPayment(array $paymentData): array
    {
        // Mock processing for PayPal
        return [
            'success' => true,
            'transaction_id' => 'PAYID-' . bin2hex(random_bytes(8)),
            'status' => 'completed',
            'provider_payload' => [
                'payer_info' => 'mock_payer@example.com',
                'payment_source' => 'paypal'
            ]
        ];
    }

    /**
     * Create a subscription
     */
    public function createSubscription(array $subscriptionData): array
    {
        // Mock processing for PayPal
        return [
            'success' => true,
            'subscription_id' => 'I-' . bin2hex(random_bytes(8)),
            'status' => 'active',
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => [
                'plan_id' => $subscriptionData['plan_id'],
                'billing_agreement_id' => 'MOCK-AGR-' . bin2hex(random_bytes(4))
            ]
        ];
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        // Mock canceling for PayPal
        return true;
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): string
    {
        return 'completed';
    }

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(string $subscriptionId): string
    {
        return 'active';
    }

    /**
     * Check if the integration is healthy and accessible
     */
    public function healthCheck(): bool
    {
        // For a mock, it is always healthy
        return true;
    }

    /**
     * Get integration-specific capabilities
     */
    public function getCapabilities(): array
    {
        return [
            'single_payments' => true,
            'subscriptions' => true,
            'refunds' => true,
            'webhooks' => true
        ];
    }
}
