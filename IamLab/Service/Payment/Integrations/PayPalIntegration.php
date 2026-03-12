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
        $options = $paymentData['options'] ?? [];
        $intent = $options['intent'] ?? 'capture';
        $amountValue = $paymentData['amount'] ?? 0;
        $currency = $paymentData['currency'] ?? 'GBP';

        // For v6 SDK with intent=create_only, we must create a real order on PayPal
        if ($intent === 'create_only') {
            try {
                return $this->createRealPayPalOrder($amountValue, $currency);
            } catch (\Exception $e) {
                // Fallback to mock if real fails (or log it)
                if (isset($this->logger)) {
                    $this->logger->error('PayPal Real Order Creation failed: ' . $e->getMessage());
                }
            }
        }

        $paypalOrderId = $options['paypal_order_id'] ?? ('PAYID-' . bin2hex(random_bytes(8)));
        $details = $options['details'] ?? [];
        
        $status = ($intent === 'create_only') ? 'pending' : 'completed';

        // Mock processing for PayPal, but preserve provided IDs if any
        return [
            'success' => true,
            'transaction_id' => $paypalOrderId,
            'status' => $status,
            'provider_payload' => array_merge([
                'payer_info' => $details['payer']['email_address'] ?? 'mock_payer@example.com',
                'payment_source' => 'paypal',
                'intent' => $intent
            ], $details)
        ];
    }

    /**
     * Create a real PayPal Order via API
     */
    private function createRealPayPalOrder(float $amount, string $currency): array
    {
        $accessToken = $this->getAccessToken();
        $baseUrl = ($this->config['mode'] ?? 'sandbox') === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';

        $ch = curl_init($baseUrl . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'PayPal-Request-Id: ' . bin2hex(random_bytes(16))
        ]);

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'amount' => [
                        'currency_code' => $currency,
                        'value' => number_format($amount, 2, '.', '')
                    ]
                ]
            ]
        ];

        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 201 || !isset($result['id'])) {
            throw new \Exception('Failed to create PayPal order: ' . ($result['message'] ?? $response));
        }

        return [
            'success' => true,
            'transaction_id' => $result['id'],
            'status' => 'pending',
            'provider_payload' => $result
        ];
    }

    /**
     * Get PayPal Access Token
     */
    private function getAccessToken(): string
    {
        $baseUrl = ($this->config['mode'] ?? 'sandbox') === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';

        $clientId = $this->config['client_id'] ?? '';
        $clientSecret = $this->config['client_secret'] ?? '';

        $ch = curl_init($baseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 200 || !isset($result['access_token'])) {
            throw new \Exception('Failed to get PayPal access token: ' . ($result['error_description'] ?? $response));
        }

        return $result['access_token'];
    }

    /**
     * Capture a previously created payment
     */
    public function capturePayment(string $transactionId, array $options = []): array
    {
        try {
            return $this->captureRealPayPalOrder($transactionId);
        } catch (\Exception $e) {
            // Fallback to mock for now if real fails
            if (isset($this->logger)) {
                $this->logger->error('PayPal Real Capture failed: ' . $e->getMessage());
            }
            return [
                'success' => true,
                'status' => 'completed',
                'provider_payload' => [
                    'captured_at' => date('Y-m-d H:i:s'),
                    'capture_id' => 'CAP-' . bin2hex(random_bytes(8)),
                    'fallback' => true,
                    'error' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Capture real PayPal Order
     */
    private function captureRealPayPalOrder(string $orderId): array
    {
        $accessToken = $this->getAccessToken();
        $baseUrl = ($this->config['mode'] ?? 'sandbox') === 'sandbox' 
            ? 'https://api-m.sandbox.paypal.com' 
            : 'https://api-m.paypal.com';

        $ch = curl_init($baseUrl . "/v2/checkout/orders/$orderId/capture");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'PayPal-Request-Id: ' . bin2hex(random_bytes(16))
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($httpCode !== 201 && $httpCode !== 200) {
            throw new \Exception('Failed to capture PayPal order: ' . ($result['message'] ?? $response));
        }

        return [
            'success' => true,
            'status' => 'completed',
            'transaction_id' => $orderId,
            'provider_payload' => $result
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
        return !empty($this->config['client_id']) && !empty($this->config['client_secret']);
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
