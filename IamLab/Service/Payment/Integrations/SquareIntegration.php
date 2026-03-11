<?php

namespace IamLab\Service\Payment\Integrations;

/**
 * Square Integration (Mock)
 * 
 * Implements a mock Square integration for the payment system
 */
class SquareIntegration implements PaymentIntegrationInterface
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
        // Mock processing for Square
        return [
            'success' => true,
            'transaction_id' => 'SQ-' . bin2hex(random_bytes(8)),
            'status' => 'completed',
            'provider_payload' => [
                'receipt_url' => 'https://square.com/receipt/mock',
                'payment_source' => 'square'
            ]
        ];
    }

    /**
     * Capture a previously created payment
     */
    public function capturePayment(string $transactionId, array $options = []): array
    {
        return [
            'success' => true,
            'status' => 'completed',
            'provider_payload' => ['message' => 'Square payment captured (mock)']
        ];
    }

    /**
     * Create a subscription
     */
    public function createSubscription(array $subscriptionData): array
    {
        // Mock processing for Square
        return [
            'success' => true,
            'subscription_id' => 'SQ-SUB-' . bin2hex(random_bytes(8)),
            'status' => 'active',
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 year')),
            'provider_payload' => [
                'plan_id' => $subscriptionData['plan_id'],
                'customer_id' => 'SQ-CUST-' . bin2hex(random_bytes(4))
            ]
        ];
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        // Mock canceling for Square
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
