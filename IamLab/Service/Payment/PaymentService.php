<?php

namespace IamLab\Service\Payment;

use IamLab\Model\Payment;
use IamLab\Model\Subscription;
use IamLab\Service\Payment\Integrations\PaymentIntegrationInterface;
use IamLab\Service\Payment\Configuration\ConfigurationManager;
use IamLab\Service\Payment\Registry\IntegrationRegistry;
use Exception;

class PaymentService
{
    protected ?PaymentIntegrationInterface $integration = null;
    protected string $currentProvider;
    protected IntegrationRegistry $registry;

    public function __construct(string $provider = 'stripe', array $config = [])
    {
        $this->registry = new IntegrationRegistry(new ConfigurationManager());
        $this->setProvider($provider);
    }

    /**
     * Set the current payment provider
     */
    public function setProvider(string $provider): self
    {
        $this->currentProvider = $provider;
        $this->integration = $this->registry->getIntegration($provider);
        return $this;
    }

    /**
     * Get available providers
     */
    public function getAvailableProviders(): array
    {
        return $this->registry->getAvailableProviders();
    }

    /**
     * Process a single payment
     */
    public function processSinglePayment(int $userId, float $amount, string $currency = 'USD', array $options = []): Payment
    {
        if (!$this->integration) {
            throw new Exception("Payment integration not initialized");
        }

        $paymentData = [
            'amount' => $amount,
            'currency' => $currency,
            'options' => $options
        ];

        $result = $this->integration->createPayment($paymentData);

        $payment = new Payment();
        $payment->setUserId($userId);
        $payment->setPaymentMethod($this->currentProvider);
        $payment->setTransactionId($result['transaction_id']);
        $payment->setAmount($amount);
        $payment->setCurrency($currency);
        $payment->setStatus($result['status']);
        $payment->setType('single');
        $payment->setPayload(json_encode($result['provider_payload']));

        if (!$payment->save()) {
            throw new Exception("Failed to save payment record: " . implode(', ', $payment->getMessages()));
        }

        return $payment;
    }

    /**
     * Create a subscription
     */
    public function createSubscription(int $userId, string $planId, array $options = []): Subscription
    {
        if (!$this->integration) {
            throw new Exception("Payment integration not initialized");
        }

        $subscriptionData = [
            'plan_id' => $planId,
            'options' => $options
        ];

        $result = $this->integration->createSubscription($subscriptionData);

        $subscription = new Subscription();
        $subscription->setUserId($userId);
        $subscription->setPaymentMethod($this->currentProvider);
        $subscription->setSubscriptionId($result['subscription_id']);
        $subscription->setPlanId($planId);
        $subscription->setStatus($result['status']);
        $subscription->setStartsAt($result['starts_at']);
        $subscription->setEndsAt($result['ends_at']);
        $subscription->setPayload(json_encode($result['provider_payload']));

        if (!$subscription->save()) {
            throw new Exception("Failed to save subscription record: " . implode(', ', $subscription->getMessages()));
        }

        return $subscription;
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription(int $subscriptionId): bool
    {
        $subscription = Subscription::findFirstById($subscriptionId);
        if (!$subscription) {
            throw new Exception("Subscription not found");
        }

        if (!$this->integration) {
            throw new Exception("Payment integration not initialized");
        }

        $success = $this->integration->cancelSubscription($subscription->getSubscriptionId());

        if ($success) {
            $subscription->setStatus('canceled');
            $subscription->setCanceledAt(date('Y-m-d H:i:s'));
            return $subscription->save();
        }

        return false;
    }

    /**
     * Get user payments
     */
    public function getUserPayments(int $userId)
    {
        return Payment::find([
            'conditions' => 'user_id = :userId:',
            'bind' => ['userId' => $userId],
            'order' => 'created_at DESC'
        ]);
    }

    /**
     * Get user subscriptions
     */
    public function getUserSubscriptions(int $userId)
    {
        return Subscription::find([
            'conditions' => 'user_id = :userId:',
            'bind' => ['userId' => $userId],
            'order' => 'created_at DESC'
        ]);
    }
}
