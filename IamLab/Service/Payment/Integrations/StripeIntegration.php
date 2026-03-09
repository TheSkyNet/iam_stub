<?php

namespace IamLab\Service\Payment\Integrations;

class StripeIntegration implements PaymentIntegrationInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function createPayment(array $paymentData): array
    {
        // Skeleton implementation
        return [
            'transaction_id' => 'st_' . uniqid(),
            'status' => 'completed',
            'amount' => $paymentData['amount'],
            'currency' => $paymentData['currency'] ?? 'USD',
            'provider_payload' => ['message' => 'Stripe payment created (mock)']
        ];
    }

    public function createSubscription(array $subscriptionData): array
    {
        // Skeleton implementation
        return [
            'subscription_id' => 'sub_' . uniqid(),
            'status' => 'active',
            'plan_id' => $subscriptionData['plan_id'],
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => ['message' => 'Stripe subscription created (mock)']
        ];
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        return true;
    }

    public function getPaymentStatus(string $transactionId): string
    {
        return 'completed';
    }

    public function getSubscriptionStatus(string $subscriptionId): string
    {
        return 'active';
    }

    public function healthCheck(): bool
    {
        return !empty($this->config['api_key']);
    }

    public function getCapabilities(): array
    {
        return ['single_payment', 'subscription', 'refund'];
    }
}
