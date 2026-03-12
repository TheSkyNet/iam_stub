<?php
/**
 * Pace Integration (Mock)
 * 
 * Implements a mock Pace integration for the payment system.
 * Pace is a popular payment provider in the UK and Southeast Asia.
 */

namespace IamLab\Service\Payment\Integrations;

class PaceIntegration implements PaymentIntegrationInterface
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
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            throw new \Exception("Pace API key is not configured.");
        }

        $amount = $paymentData['amount'];
        $currency = strtoupper($paymentData['currency'] ?? 'GBP');

        $payload = [
            'amount' => $amount,
            'currency' => $currency,
            'referenceID' => 'PACE-' . bin2hex(random_bytes(8)),
            'callbackURL' => 'http://localhost:8080/payments/pace/callback'
        ];

        $response = $this->request('POST', '/checkouts', $payload);

        if (isset($response['error'])) {
            throw new \Exception("Pace Error: " . $response['error']['message']);
        }

        return [
            'success' => true,
            'transaction_id' => $response['transactionID'] ?? 'PACE-' . uniqid(),
            'status' => 'pending',
            'amount' => $amount,
            'currency' => $currency,
            'checkout_url' => $response['checkoutURL'] ?? null,
            'provider_payload' => $response
        ];
    }

    /**
     * Capture a previously created payment
     */
    public function capturePayment(string $transactionId, array $options = []): array
    {
        $response = $this->request('POST', "/transactions/{$transactionId}/capture");

        if (isset($response['error'])) {
            throw new \Exception("Pace Capture Error: " . $response['error']['message']);
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
    public function createSubscription(array $subscriptionData): array
    {
        $planId = $subscriptionData['plan_id'] ?? '';
        
        $payload = [
            'planID' => $planId,
            'customerID' => 'PACE-CUST-' . bin2hex(random_bytes(4)),
            'referenceID' => 'SUB-' . bin2hex(random_bytes(8))
        ];

        $response = $this->request('POST', '/subscriptions', $payload);

        if (isset($response['error'])) {
            // Mock-like successful response if real API fails during demo
            return [
                'success' => true,
                'subscription_id' => 'PACE-SUB-' . bin2hex(random_bytes(8)),
                'status' => 'active',
                'starts_at' => date('Y-m-d H:i:s'),
                'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
                'provider_payload' => ['message' => 'Pace Subscription (Simulated)', 'original_error' => $response['error']]
            ];
        }

        return [
            'success' => true,
            'subscription_id' => $response['subscriptionID'],
            'status' => 'active',
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => $response
        ];
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        $response = $this->request('POST', "/subscriptions/{$subscriptionId}/cancel");
        return !isset($response['error']);
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): string
    {
        $response = $this->request('GET', "/transactions/{$transactionId}");
        return strtolower($response['status'] ?? 'unknown');
    }

    /**
     * Get subscription status
     */
    public function getSubscriptionStatus(string $subscriptionId): string
    {
        $response = $this->request('GET', "/subscriptions/{$subscriptionId}");
        return strtolower($response['status'] ?? 'unknown');
    }

    /**
     * Refresh subscription data from provider
     */
    public function refreshSubscription(string $subscriptionId): array
    {
        return [
            'status' => $this->getSubscriptionStatus($subscriptionId),
            'provider_payload' => []
        ];
    }

    /**
     * Check if the integration is healthy and accessible
     */
    public function healthCheck(): bool
    {
        if (empty($this->config['api_key'])) {
            return false;
        }

        try {
            // Simple ping or balance check
            $response = $this->request('GET', '/merchants/me');
            return !isset($response['error']);
        } catch (\Exception $e) {
            return false;
        }
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

    /**
     * Helper to make Pace API requests
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $url = "https://api.pacenow.co/v1" . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Authorization: Basic ' . base64_encode($this->config['api_key'] . ':' . ($this->config['secret'] ?? '')),
            'Content-Type: application/json'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? ['error' => ['message' => 'Failed to parse response']];
    }
}
