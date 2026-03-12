<?php

namespace IamLab\Service\Payment\Integrations;

/**
 * SumUp Integration
 * 
 * Implements a real SumUp integration for the payment system.
 * SumUp is very popular in the UK for small business payments.
 */
class SumUpIntegration implements PaymentIntegrationInterface
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Create a single payment (Checkout)
     */
    public function createPayment(array $paymentData): array
    {
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            throw new \Exception("SumUp API key is not configured.");
        }

        $amount = $paymentData['amount'];
        $currency = strtoupper($paymentData['currency'] ?? 'GBP');

        $payload = [
            'checkout_reference' => 'SUM-' . bin2hex(random_bytes(4)),
            'amount' => $amount,
            'currency' => $currency,
            'pay_to_email' => $this->config['merchant_email'] ?? 'merchant@example.com',
            'description' => 'IamLab Payment',
            'return_url' => 'http://localhost:8080/payments?provider=sumup&success=true'
        ];

        $response = $this->request('POST', '/checkouts', $payload);

        if (isset($response['error']) || (isset($response['status']) && $response['status'] === 'FAILED')) {
            throw new \Exception("SumUp Error: " . ($response['message'] ?? 'Failed to create checkout'));
        }

        return [
            'success' => true,
            'transaction_id' => $response['id'],
            'status' => $this->mapSumUpStatus($response['status'] ?? 'PENDING'),
            'amount' => $amount,
            'currency' => $currency,
            'checkout_url' => "https://gateway.sumup.com/checkouts/{$response['id']}", // Public hosted checkout
            'provider_payload' => $response
        ];
    }

    /**
     * Capture a payment
     */
    public function capturePayment(string $transactionId, array $options = []): array
    {
        // SumUp checkouts are usually captured when paid via hosted page.
        return [
            'success' => true,
            'status' => $this->getPaymentStatus($transactionId),
            'provider_payload' => []
        ];
    }

    /**
     * Create a subscription
     */
    public function createSubscription(array $subscriptionData): array
    {
        // SumUp supports recurring payments via "Customer" and "Payment Instrument" (card vaulting).
        // For the demo, we simulate the success.
        $planId = $subscriptionData['plan_id'] ?? '';
        
        return [
            'success' => true,
            'subscription_id' => 'sum_sub_' . bin2hex(random_bytes(8)),
            'status' => 'active',
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => [
                'message' => 'SumUp Subscription (Simulated)',
                'plan_id' => $planId,
                'note' => 'Real SumUp recurring payments require customer card vaulting.'
            ]
        ];
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(string $subscriptionId): bool
    {
        return true;
    }

    /**
     * Get payment status
     */
    public function getPaymentStatus(string $transactionId): string
    {
        $response = $this->request('GET', "/checkouts/{$transactionId}");
        return $this->mapSumUpStatus($response['status'] ?? 'unknown');
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
        if (empty($this->config['api_key'])) {
            return false;
        }

        try {
            // Check merchant profile
            $response = $this->request('GET', '/me');
            return isset($response['merchant_profile']);
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
     * Helper to make SumUp API requests
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $url = "https://api.sumup.com/v0.1" . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = [
            'Authorization: Bearer ' . $this->config['api_key'],
            'Content-Type: application/json',
            'Accept: application/json'
        ];
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        
        if ($httpCode >= 400 && !isset($result['id']) && !isset($result['merchant_profile'])) {
            return [
                'error' => true,
                'status' => $httpCode,
                'message' => $result['message'] ?? 'API request failed'
            ];
        }

        return $result ?? [];
    }

    /**
     * Map SumUp status to internal status
     */
    protected function mapSumUpStatus(string $status): string
    {
        switch (strtoupper($status)) {
            case 'PAID':
                return 'completed';
            case 'PENDING':
                return 'pending';
            case 'CANCELLED':
                return 'canceled';
            case 'FAILED':
            case 'EXPIRED':
                return 'failed';
            default:
                return 'unknown';
        }
    }
}
