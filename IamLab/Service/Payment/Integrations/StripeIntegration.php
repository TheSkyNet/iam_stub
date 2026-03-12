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
        $currency = strtolower($paymentData['currency'] ?? 'gbp');

        // Stripe expects amounts in cents
        $amountInCents = (int)($amount * 100);

        $payload = [
            'amount' => $amountInCents,
            'currency' => $currency,
            'payment_method_types' => ['card'],
        ];

        // Add optional payment method from options if present for backend-initiated confirmation
        if (isset($paymentData['options']['payment_method'])) {
            $payload['payment_method'] = $paymentData['options']['payment_method'];
            $payload['confirm'] = true;
            // return_url is required for confirm: true for some payment methods
            $payload['return_url'] = 'http://localhost:8080/payments/confirm';
        }

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
        // Check current status first. If already succeeded, no need to capture.
        $statusResponse = $this->request('GET', "/payment_intents/{$transactionId}");
        if (isset($statusResponse['status']) && $statusResponse['status'] === 'succeeded') {
            return [
                'success' => true,
                'status' => 'completed',
                'provider_payload' => $statusResponse
            ];
        }

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
        $planId = $subscriptionData['plan_id'] ?? '';
        $currency = strtolower($subscriptionData['options']['currency'] ?? 'gbp');
        $amount = (float)($subscriptionData['options']['amount'] ?? 25.00);

        // Option 1: Direct Subscription (if we want to use Elements/Card on the page)
        // We trigger this if the request doesn't explicitly ask for checkout or if we want to provide a client_secret
        if (isset($subscriptionData['options']['use_elements']) && $subscriptionData['options']['use_elements']) {
            return $this->createDirectSubscription($planId, $amount, $currency);
        }

        // Option 2: Checkout Session (Default for now, but now properly promoted)
        $payload = [
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => "Plan: " . $planId,
                    ],
                    'unit_amount' => (int)($amount * 100),
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
            'subscription_id' => $response['id'], // Checkout session ID
            'checkout_url' => $response['url'],
            'status' => 'pending',
            'plan_id' => $planId,
            'starts_at' => date('Y-m-d H:i:s'),
            'ends_at' => date('Y-m-d H:i:s', strtotime('+1 month')),
            'provider_payload' => $response
        ];
    }

    /**
     * Create a subscription directly using the Subscriptions API
     * Returns a client_secret for the first payment
     */
    protected function createDirectSubscription(string $planId, float $amount, string $currency): array
    {
        // 1. Get or Create Customer
        $customerResponse = $this->request('POST', '/customers', [
            'description' => 'IamLab Demo Customer',
            'email' => 'demo@example.com' // In real app, use user's email
        ]);
        $customerId = $customerResponse['id'] ?? '';

        // 2. Get or Create Price
        $priceId = $this->getOrCreatePrice($planId, $amount, $currency);

        // 3. Create Subscription
        $payload = [
            'customer' => $customerId,
            'items' => [['price' => $priceId]],
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
            'expand' => ['latest_invoice.payment_intent'],
        ];

        $response = $this->request('POST', '/subscriptions', $payload);

        if (isset($response['error'])) {
            throw new \Exception("Stripe Direct Subscription Error: " . $response['error']['message']);
        }

        $paymentIntent = $response['latest_invoice']['payment_intent'] ?? null;

        return [
            'success' => true,
            'subscription_id' => $response['id'],
            'status' => $this->mapStripeStatus($response['status']),
            'client_secret' => $paymentIntent['client_secret'] ?? null,
            'plan_id' => $planId,
            'starts_at' => date('Y-m-d H:i:s', $response['current_period_start'] ?? time()),
            'ends_at' => date('Y-m-d H:i:s', $response['current_period_end'] ?? strtotime('+1 month')),
            'provider_payload' => $response
        ];
    }

    /**
     * Helper to get or create a Price for a plan
     */
    protected function getOrCreatePrice(string $planId, float $amount, string $currency): string
    {
        $productId = 'prod_' . preg_replace('/[^a-zA-Z0-9]/', '_', $planId);
        
        // Ensure Product exists
        $this->request('POST', '/products', [
            'id' => $productId,
            'name' => 'Plan: ' . $planId
        ]);

        // Create Price
        $priceResponse = $this->request('POST', '/prices', [
            'unit_amount' => (int)($amount * 100),
            'currency' => strtolower($currency),
            'recurring' => ['interval' => 'month'],
            'product' => $productId
        ]);

        return $priceResponse['id'] ?? '';
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

    public function refreshSubscription(string $subscriptionId): array
    {
        // If it's a checkout session, try to get the subscription ID from it
        if (strpos($subscriptionId, 'cs_') === 0) {
            $session = $this->request('GET', "/checkout/sessions/{$subscriptionId}");
            if (isset($session['subscription']) && !empty($session['subscription'])) {
                $subscriptionId = $session['subscription'];
                // Now get the real subscription status
                $subscription = $this->request('GET', "/subscriptions/{$subscriptionId}");
                return [
                    'status' => $subscription['status'] ?? 'pending',
                    'subscription_id' => $subscriptionId,
                    'provider_payload' => array_merge($session, $subscription)
                ];
            }
            return [
                'status' => 'pending',
                'provider_payload' => $session
            ];
        }

        // Regular subscription refresh
        $subscription = $this->request('GET', "/subscriptions/{$subscriptionId}");
        return [
            'status' => $subscription['status'] ?? 'unknown',
            'provider_payload' => $subscription
        ];
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
            case 'requires_capture':
                return 'pending';
            case 'canceled':
                return 'canceled';
            default:
                return 'failed';
        }
    }
}
