<?php

namespace IamLab\Service\Payment\Integrations;

/**
 * Payment Integration Interface
 * 
 * Defines the contract that all payment integrations must implement
 */
interface PaymentIntegrationInterface
{
    /**
     * Initialize the integration with configuration
     */
    public function __construct(array $config);

    /**
     * Create a single payment
     */
    public function createPayment(array $paymentData): array;

    /**
     * Capture a previously created payment
     */
    public function capturePayment(string $transactionId, array $options = []): array;

    /**
     * Create a subscription
     */
    public function createSubscription(array $subscriptionData): array;

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool;

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): string;

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(string $subscriptionId): string;

    /**
     * Refresh subscription data from provider
     */
    public function refreshSubscription(string $subscriptionId): array;

    /**
     * Check if the integration is healthy and accessible
     */
    public function healthCheck(): bool;

    /**
     * Get integration-specific capabilities
     */
    public function getCapabilities(): array;
}
