<?php

namespace IamLab\Service\Payment\Integrations;

/**
 * Square Integration (Mock)
 *
 * Implements a mock Square integration for the payment system
 */
class SquareIntegration implements PaymentIntegrationInterface
{
    public function __construct(protected array $config)
    {
    }

    /**
     * Create a single payment
     */
    #[\Override]
    public function createPayment(array $paymentData): array
    {
        $accessToken = $this->config['access_token'] ?? '';
        if (empty($accessToken)) {
            throw new \Exception("Square access token is not configured.");
        }

        $amount = $paymentData['amount'];
        $currency = strtoupper($paymentData['currency'] ?? 'GBP');
        $sourceId = $paymentData['options']['source_id'] ?? 'cnon:card-nonce-ok'; // Default to sandbox nonce

        $payload = [
            'idempotency_key' => bin2hex(random_bytes(16)),
            'amount_money' => [
                'amount' => (int)($amount * 100),
                'currency' => $currency
            ],
            'source_id' => $sourceId,
            'location_id' => $this->config['location_id'] ?? ''
        ];

        $response = $this->request('POST', '/payments', $payload);

        if (isset($response['errors'])) {
            throw new \Exception("Square Error: " . $response['errors'][0]['detail']);
        }

        $payment = $response['payment'] ?? [];

        return [
            'success' => true,
            'transaction_id' => $payment['id'] ?? 'SQ-' . uniqid(),
            'status' => $this->mapSquareStatus($payment['status'] ?? 'COMPLETED'),
            'amount' => $amount,
            'currency' => $currency,
            'provider_payload' => $response
        ];
    }

    /**
     * Capture a previously created payment
     */
    #[\Override]
    public function capturePayment(string $transactionId, array $options = []): array
    {
        $response = $this->request('POST', sprintf('/payments/%s/complete', $transactionId));

        if (isset($response['errors'])) {
            throw new \Exception("Square Capture Error: " . $response['errors'][0]['detail']);
        }

        return [
            'success' => true,
            'status' => 'completed',
            'provider_payload' => $response
        ];
    }

    /**
     * Create a subscription
     */
    #[\Override]
    public function createSubscription(array $subscriptionData): array
    {
        $planId = $subscriptionData['plan_id'] ?? '';
        $customerId = $subscriptionData['options']['customer_id'] ?? '';

        if (empty($customerId)) {
            // Mocking customer creation for demo purposes if not provided
            $customerId = 'CUST-' . bin2hex(random_bytes(4));
        }

        $payload = [
            'idempotency_key' => bin2hex(random_bytes(16)),
            'location_id' => $this->config['location_id'] ?? '',
            'plan_id' => $planId,
            'customer_id' => $customerId
        ];

        // Square subscriptions API
        $response = $this->request('POST', '/subscriptions', $payload);

        if (isset($response['errors'])) {
            // If it fails (e.g. plan doesn't exist in sandbox), return something realistic
            return [
                'success' => false,
                'message' => $response['errors'][0]['detail'],
                'provider_payload' => $response
            ];
        }

        $subscription = $response['subscription'] ?? [];

        return [
            'success' => true,
            'subscription_id' => $subscription['id'] ?? 'SQ-SUB-' . uniqid(),
            'status' => 'active',
            'starts_at' => $subscription['start_date'] ?? date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => $response
        ];
    }

    /**
     * Cancel a subscription
     */
    #[\Override]
    public function cancelSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', sprintf('/subscriptions/%s/cancel', $subscriptionId));
        return !isset($response['errors']);
    }

    /**
     * Get payment status
     */
    #[\Override]
    public function getPaymentStatus(string $transactionId): string
    {
        $response = $this->request('GET', '/payments/' . $transactionId);
        return $this->mapSquareStatus($response['payment']['status'] ?? 'unknown');
    }

    /**
     * Get subscription status
     */
    #[\Override]
    public function getSubscriptionStatus(string $subscriptionId): string
    {
        $response = $this->request('GET', '/subscriptions/' . $subscriptionId);
        return strtolower($response['subscription']['status'] ?? 'unknown');
    }

    /**
     * Refresh subscription data from provider
     */
    #[\Override]
    public function refreshSubscription(string $subscriptionId): array
    {
        $response = $this->request('GET', '/subscriptions/' . $subscriptionId);
        return [
            'status' => strtolower($response['subscription']['status'] ?? 'unknown'),
            'provider_payload' => $response
        ];
    }

    /**
     * Check if the integration is healthy and accessible
     */
    #[\Override]
    public function healthCheck(): bool
    {
        if (empty($this->config['access_token'])) {
            return false;
        }

        try {
            $response = $this->request('GET', '/locations');
            return !isset($response['errors']);
        } catch (\Exception) {
            return false;
        }
    }

    /**
     * Get integration-specific capabilities
     */
    #[\Override]
    public function getCapabilities(): array
    {
        return [
            'single_payments' => true,
            'subscriptions' => true,
            'refunds' => true,
            'webhooks' => true
        ];
    }

    /**
     * Helper to make Square API requests
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $baseUrl = "https://connect.squareupsandbox.com/v2"; // Default to sandbox
        $url = $baseUrl . $path;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $headers = [
            'Authorization: Bearer ' . ($this->config['access_token'] ?? ''),
            'Content-Type: application/json',
            'Square-Version: 2024-01-18'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? ['errors' => [['detail' => 'Failed to parse response']]];
    }

    /**
     * Map Square status to internal status
     */
    protected function mapSquareStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'COMPLETED', 'APPROVED' => 'completed',
            'PENDING' => 'pending',
            'CANCELED' => 'canceled',
            default => 'failed',
        };
    }
}
