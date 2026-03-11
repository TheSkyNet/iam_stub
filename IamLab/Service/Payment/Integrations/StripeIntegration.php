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
        $apiKey = $this->config['api_key'] ?? '';
        if (empty($apiKey)) {
            throw new \Exception("Stripe API key is not configured.");
        }

        $amount = $paymentData['amount'];
        $currency = strtolower($paymentData['currency'] ?? 'usd');

        // Stripe expects amounts in cents
        $amountInCents = (int)($amount * 100);

        $payload = [
            'amount' => $amountInCents,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ];

        $response = $this->request('POST', '/payment_intents', $payload);

        if (isset($response['error'])) {
            throw new \Exception("Stripe Error: " . $response['error']['message']);
        }

        return [
            'success' => true,
            'transaction_id' => $response['id'],
            'status' => $this->mapStripeStatus($response['status']),
            'amount' => $amount,
            'currency' => strtoupper($currency),
            'client_secret' => $response['client_secret'] ?? null,
            'provider_payload' => $response
        ];
    }

    public function capturePayment(string $transactionId, array $options = []): array
    {
        $response = $this->request('POST', "/payment_intents/{$transactionId}/capture");

        if (isset($response['error'])) {
            throw new \Exception("Stripe Capture Error: " . $response['error']['message']);
        }

        return [
            'success' => true,
            'status' => $this->mapStripeStatus($response['status']),
            'provider_payload' => $response
        ];
    }

    public function createSubscription(array $subscriptionData): array
    {
        // For a full implementation, we'd create a customer, product, price, and then subscription.
        // For the demo, we'll try to create a Checkout Session for a subscription which is easier.
        
        $planId = $subscriptionData['plan_id'] ?? '';
        
        $payload = [
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => "Plan: " . $planId,
                    ],
                    'unit_amount' => 2500, // $25.00
                    'recurring' => ['interval' => 'month'],
                ],
                'quantity' => 1,
            ]],
            'success_url' => 'http://localhost:8080/payments?success=true',
            'cancel_url' => 'http://localhost:8080/payments?canceled=true',
        ];

        $response = $this->request('POST', '/checkout/sessions', $payload);

        if (isset($response['error'])) {
            throw new \Exception("Stripe Subscription Error: " . $response['error']['message']);
        }

        return [
            'success' => true,
            'subscription_id' => $response['id'], // Checkout session ID in this case
            'checkout_url' => $response['url'],
            'status' => 'pending',
            'plan_id' => $planId,
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => $response
        ];
    }

    public function cancelSubscription(string $subscriptionId): bool
    {
        $response = $this->request('DELETE', "/subscriptions/{$subscriptionId}");
        return !isset($response['error']);
    }

    public function getPaymentStatus(string $transactionId): string
    {
        $response = $this->request('GET', "/payment_intents/{$transactionId}");
        return $this->mapStripeStatus($response['status'] ?? 'unknown');
    }

    public function getSubscriptionStatus(string $subscriptionId): string
    {
        $response = $this->request('GET', "/subscriptions/{$subscriptionId}");
        return $response['status'] ?? 'unknown';
    }

    public function healthCheck(): bool
    {
        if (empty($this->config['api_key'])) {
            return false;
        }

        try {
            $response = $this->request('GET', '/balance');
            return !isset($response['error']);
        } catch (\Exception $e) {
            return false;
        }
    }

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
     * Helper to make Stripe API requests
     */
    protected function request(string $method, string $path, array $data = []): array
    {
        $url = "https://api.stripe.com/v1" . $path;
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->config['api_key'] . ":");

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true) ?? ['error' => ['message' => 'Failed to parse response']];
    }

    /**
     * Map Stripe status to internal status
     */
    protected function mapStripeStatus(string $status): string
    {
        switch ($status) {
            case 'succeeded':
                return 'completed';
            case 'requires_payment_method':
            case 'requires_confirmation':
            case 'requires_action':
            case 'processing':
                return 'pending';
            case 'canceled':
                return 'canceled';
            default:
                return 'failed';
        }
    }
}
