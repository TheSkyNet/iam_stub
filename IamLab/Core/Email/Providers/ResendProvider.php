<?php

namespace IamLab\Core\Email\Providers;

use IamLab\Core\Email\EmailProviderInterface;
use Exception;

class ResendProvider implements EmailProviderInterface
{
    private array $config;
    private string $lastError = '';

    public function __construct(array $config)
    {
        $this->config = $config['resend'] ?? [];
    }

    /**
     * Send an email using Resend API
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $options Additional options
     * @return bool True if email was sent successfully
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        try {
            $fromEmail = $options['from_email'] ?? 'noreply@example.com';
            $fromName = $options['from_name'] ?? 'Phalcon Stub';
            $isHtml = $options['is_html'] ?? true;

            $payload = [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => [$to],
                'subject' => $subject,
            ];

            // Set content type
            if ($isHtml) {
                $payload['html'] = $body;
            } else {
                $payload['text'] = $body;
            }

            // Add optional fields
            if (!empty($options['reply_to'])) {
                $payload['reply_to'] = [$options['reply_to']];
            }

            if (!empty($options['cc'])) {
                $payload['cc'] = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
            }

            if (!empty($options['bcc'])) {
                $payload['bcc'] = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
            }

            if (!empty($options['tags'])) {
                $payload['tags'] = is_array($options['tags']) ? $options['tags'] : [$options['tags']];
            }

            $response = $this->makeApiRequest('/emails', $payload);

            if (!$response) {
                return false;
            }

            return true;
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
        try {
            $fromEmail = $options['from_email'] ?? 'noreply@example.com';
            $fromName = $options['from_name'] ?? 'Phalcon Stub';
            $isHtml = $options['is_html'] ?? true;

            $payload = [
                'from' => "{$fromName} <{$fromEmail}>",
                'to' => $recipients,
                'subject' => $subject,
            ];

            // Set content type
            if ($isHtml) {
                $payload['html'] = $body;
            } else {
                $payload['text'] = $body;
            }

            // Add optional fields
            if (!empty($options['reply_to'])) {
                $payload['reply_to'] = [$options['reply_to']];
            }

            if (!empty($options['cc'])) {
                $payload['cc'] = is_array($options['cc']) ? $options['cc'] : [$options['cc']];
            }

            if (!empty($options['bcc'])) {
                $payload['bcc'] = is_array($options['bcc']) ? $options['bcc'] : [$options['bcc']];
            }

            if (!empty($options['tags'])) {
                $payload['tags'] = is_array($options['tags']) ? $options['tags'] : [$options['tags']];
            }

            $response = $this->makeApiRequest('/emails', $payload);

            if (!$response) {
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Validate Resend configuration
     *
     * @return bool True if configuration is valid
     */
    public function validateConfig(): bool
    {
        $apiKey = $this->config['api_key'] ?? '';

        if (empty($apiKey)) {
            $this->lastError = "Resend API key is required";
            return false;
        }

        if (!str_starts_with($apiKey, 're_')) {
            $this->lastError = "Invalid Resend API key format";
            return false;
        }

        return true;
    }

    /**
     * Get the last error message
     *
     * @return string|null Last error message or null if no error
     */
    public function getLastError(): ?string
    {
        return $this->lastError ?: null;
    }

    /**
     * Make API request to Resend
     *
     * @param string $endpoint API endpoint
     * @param array $payload Request payload
     * @return array|false Response data or false on failure
     */
    private function makeApiRequest(string $endpoint, array $payload)
    {
        try {
            $apiKey = $this->config['api_key'] ?? '';
            $baseUrl = $this->config['endpoint'] ?? 'https://api.resend.com';
            $url = $baseUrl . $endpoint;

            $headers = [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
                'User-Agent: Phalcon-Stub-Email-Service/1.0'
            ];

            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_MAXREDIRS => 0,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                $this->lastError = "cURL error: {$curlError}";
                return false;
            }

            if ($httpCode >= 400) {
                $errorData = json_decode($response, true);
                $errorMessage = $errorData['message'] ?? "HTTP error {$httpCode}";
                $this->lastError = "Resend API error: {$errorMessage}";
                return false;
            }

            $responseData = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->lastError = "Invalid JSON response from Resend API";
                return false;
            }

            return $responseData;
        } catch (Exception $e) {
            $this->lastError = "API request failed: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Test the API connection
     *
     * @return bool True if connection is successful
     */
    public function testConnection(): bool
    {
        try {
            // Make a simple API call to test the connection
            $response = $this->makeApiRequest('/domains', []);
            return $response !== false;
        } catch (Exception $e) {
            $this->lastError = "Connection test failed: " . $e->getMessage();
            return false;
        }
    }
}