<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Service\Payment\PaymentService;

class PaymentsApi extends aAPI
{
    protected PaymentService $paymentService;

    public function initialize()
    {
        $this->paymentService = new PaymentService();
    }

    /**
     * Create a single payment
     * POST /api/payments
     */
    public function createAction(): void
    {
        $this->requireAuth();
        
        try {
            $data = $this->getData();
            $amount = $data['amount'] ?? null;
            $currency = $data['currency'] ?? 'USD';
            $provider = $data['provider'] ?? 'stripe';
            $transactionId = $data['paypal_order_id'] ?? $data['transaction_id'] ?? null;
            
            $user = $this->getCurrentUser();
            $this->paymentService->setProvider($provider);

            if ($transactionId && (isset($data['status']) && $data['status'] === 'captured')) {
                $payment = $this->paymentService->capturePayment($transactionId, $data);
                $message = 'Payment captured successfully';
            } else {
                if (!$amount) {
                    $this->dispatchError([
                        'success' => false,
                        'message' => 'Amount is required'
                    ]);
                    return;
                }

                $payment = $this->paymentService->processSinglePayment($user->getId(), (float)$amount, $currency, $data);
                $message = 'Payment processed successfully';
            }

            $this->dispatch([
                'success' => true,
                'data' => $payment->toArray(),
                'message' => $message
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create a subscription
     * POST /api/subscriptions
     */
    public function createSubscriptionAction(): void
    {
        $this->requireAuth();
        
        try {
            $data = $this->getData();
            $planId = $data['plan_id'] ?? null;
            $provider = $data['provider'] ?? 'stripe';
            
            if (!$planId) {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Plan ID is required'
                ]);
                return;
            }

            $user = $this->getCurrentUser();
            $this->paymentService->setProvider($provider);
            $subscription = $this->paymentService->createSubscription($user->getId(), $planId, $data);

            $this->dispatch([
                'success' => true,
                'data' => $subscription->toArray(),
                'message' => 'Subscription created successfully'
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to create subscription',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Cancel a subscription
     * DELETE /api/subscriptions/:id
     */
    public function cancelSubscriptionAction(string $id): void
    {
        $this->requireAuth();
        
        try {
            $success = $this->paymentService->cancelSubscription((int)$id);

            if ($success) {
                $this->dispatch([
                    'success' => true,
                    'message' => 'Subscription canceled successfully'
                ]);
            } else {
                $this->dispatchError([
                    'success' => false,
                    'message' => 'Failed to cancel subscription'
                ]);
            }
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Error canceling subscription',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user payments
     * GET /api/payments
     */
    public function indexAction(): void
    {
        $this->requireAuth();
        
        try {
            $user = $this->getCurrentUser();
            $payments = $this->paymentService->getUserPayments($user->getId());

            $this->dispatch([
                'success' => true,
                'data' => $payments->toArray()
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve payments',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user subscriptions
     * GET /api/subscriptions
     */
    public function subscriptionsAction(): void
    {
        $this->requireAuth();
        
        try {
            $user = $this->getCurrentUser();
            $subscriptions = $this->paymentService->getUserSubscriptions($user->getId());

            $this->dispatch([
                'success' => true,
                'data' => $subscriptions->toArray()
            ]);
        } catch (Exception $e) {
            $this->dispatchError([
                'success' => false,
                'message' => 'Failed to retrieve subscriptions',
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get PayPal configuration for frontend SDK
     * GET /api/payments/paypal-config
     */
    public function paypalConfigAction(): void
    {
        $this->requireAuth();
        
        $configManager = new \IamLab\Service\Payment\Configuration\ConfigurationManager();
        $paypalConfig = $configManager->getIntegrationConfig('paypal');
        
        $this->dispatch([
            'success' => true,
            'data' => [
                'clientId' => $paypalConfig['client_id'] ?? '',
                'mode' => $paypalConfig['mode'] ?? 'sandbox'
            ]
        ]);
    }

    /**
     * Get available providers
     * GET /api/payments/providers
     */
    public function providersAction(): void
    {
        $this->dispatch([
            'success' => true,
            'data' => $this->paymentService->getAvailableProviders()
        ]);
    }
}
