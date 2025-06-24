<?php

namespace IamLab\Core\Email;

use Phalcon\Di\Injectable;
use IamLab\Core\Email\Providers\MailHogProvider;
use IamLab\Core\Email\Providers\ResendProvider;
use Exception;

class EmailService extends Injectable
{
    private ?EmailProviderInterface $provider = null;
    private array $config;
    private string $lastError = '';

    public function __construct()
    {
        $this->config = $this->getDI()->getShared('config')->email->toArray();
        $this->initializeProvider();
    }

    /**
     * Initialize the email provider based on configuration
     */
    private function initializeProvider(): void
    {
        $providerName = $this->config['provider'] ?? 'mailhog';

        try {
            switch ($providerName) {
                case 'mailhog':
                    $this->provider = new MailHogProvider($this->config);
                    break;
                case 'resend':
                    $this->provider = new ResendProvider($this->config);
                    break;
                default:
                    throw new Exception("Unsupported email provider: {$providerName}");
            }

            if (!$this->provider->validateConfig()) {
                throw new Exception("Invalid configuration for email provider: {$providerName}");
            }
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            // Fallback to a null provider or log the error
            error_log("Email service initialization failed: " . $e->getMessage());
        }
    }

    /**
     * Send an email
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $options Additional options
     * @return bool True if email was sent successfully
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        if (!$this->provider) {
            $this->lastError = "No email provider available";
            return false;
        }

        // Add default from email and name if not provided
        if (!isset($options['from_email'])) {
            $options['from_email'] = $this->config['from_email'] ?? 'noreply@example.com';
        }
        if (!isset($options['from_name'])) {
            $options['from_name'] = $this->config['from_name'] ?? 'Phalcon Stub';
        }

        try {
            $result = $this->provider->send($to, $subject, $body, $options);
            if (!$result) {
                $this->lastError = $this->provider->getLastError() ?? 'Unknown error occurred';
            }
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Send an email to multiple recipients
     *
     * @param array $recipients Array of recipient email addresses
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $options Additional options
     * @return bool True if email was sent successfully
     */
    public function sendBulk(array $recipients, string $subject, string $body, array $options = []): bool
    {
        if (!$this->provider) {
            $this->lastError = "No email provider available";
            return false;
        }

        // Add default from email and name if not provided
        if (!isset($options['from_email'])) {
            $options['from_email'] = $this->config['from_email'] ?? 'noreply@example.com';
        }
        if (!isset($options['from_name'])) {
            $options['from_name'] = $this->config['from_name'] ?? 'Phalcon Stub';
        }

        try {
            $result = $this->provider->sendBulk($recipients, $subject, $body, $options);
            if (!$result) {
                $this->lastError = $this->provider->getLastError() ?? 'Unknown error occurred';
            }
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Get the last error message
     *
     * @return string Last error message
     */
    public function getLastError(): string
    {
        return $this->lastError;
    }

    /**
     * Get the current provider name
     *
     * @return string Current provider name
     */
    public function getProviderName(): string
    {
        return $this->config['provider'] ?? 'unknown';
    }

    /**
     * Check if the email service is properly configured
     *
     * @return bool True if service is ready to send emails
     */
    public function isReady(): bool
    {
        return $this->provider !== null && $this->provider->validateConfig();
    }
}