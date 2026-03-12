<?php

namespace IamLab\Service\Payment\Factory;

use Exception;
use IamLab\Service\Payment\Integrations\PaymentIntegrationInterface;
use IamLab\Service\Payment\Integrations\StripeIntegration;
use IamLab\Service\Payment\Integrations\PayPalIntegration;
use IamLab\Service\Payment\Integrations\SquareIntegration;
use IamLab\Service\Payment\Integrations\PaceIntegration;
use IamLab\Service\Payment\Integrations\MollieIntegration;
use IamLab\Service\Payment\Integrations\RevolutIntegration;

/**
 * Payment Integration Factory
 * 
 * Responsible for creating payment integration instances
 */
class IntegrationFactory
{
    /**
     * Create multiple integrations from configurations
     */
    public static function createFromConfig(array $configurations): array
    {
        $integrations = [];
        $errors = [];

        foreach ($configurations as $name => $config) {
            if (!($config['enabled'] ?? false)) {
                continue;
            }

            try {
                $integrations[$name] = self::create($name, $config);
            } catch (Exception $e) {
                $errors[$name] = $e->getMessage();
            }
        }

        return [
            'integrations' => $integrations,
            'errors' => $errors
        ];
    }

    /**
     * Create specific integration by name
     */
    public static function create(string $name, array $config): PaymentIntegrationInterface
    {
        switch (strtolower($name)) {
            case 'stripe':
                return new StripeIntegration($config);
            case 'paypal':
                return new PayPalIntegration($config);
            case 'square':
                return new SquareIntegration($config);
            case 'pace':
                return new PaceIntegration($config);
            case 'mollie':
                return new MollieIntegration($config);
            case 'revolut':
                return new RevolutIntegration($config);
            default:
                throw new Exception("Unknown payment provider: {$name}");
        }
    }

    /**
     * Get integration capabilities by name
     */
    public static function getIntegrationCapabilities(string $name): array
    {
        return [
            'single_payments' => true,
            'subscriptions' => true,
            'refunds' => true,
            'webhooks' => true
        ];
    }
}
