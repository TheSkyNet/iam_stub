<?php

namespace IamLab\Core\Email\Providers;

use IamLab\Core\Email\EmailProviderInterface;
use Exception;

class MailHogProvider implements EmailProviderInterface
{
    private array $config;
    private string $lastError = '';

    public function __construct(array $config)
    {
        $this->config = $config['mailhog'] ?? [];
    }

    /**
     * Send an email using MailHog SMTP
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

            // Create email headers
            $headers = $this->buildHeaders($fromEmail, $fromName, $isHtml, $options);

            // For MailHog, we can use PHP's mail() function with custom headers
            // or implement SMTP directly. For simplicity, we'll use mail() with custom headers
            $success = $this->sendViaSMTP($to, $subject, $body, $headers, $fromEmail);

            if (!$success) {
                $this->lastError = "Failed to send email via MailHog";
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
        $allSuccess = true;
        $errors = [];

        foreach ($recipients as $recipient) {
            if (!$this->send($recipient, $subject, $body, $options)) {
                $allSuccess = false;
                $errors[] = "Failed to send to {$recipient}: " . $this->getLastError();
            }
        }

        if (!$allSuccess) {
            $this->lastError = implode('; ', $errors);
        }

        return $allSuccess;
    }

    /**
     * Validate MailHog configuration
     *
     * @return bool True if configuration is valid
     */
    public function validateConfig(): bool
    {
        // MailHog doesn't require authentication, so we just need host and port
        $host = $this->config['host'] ?? '';
        $port = $this->config['port'] ?? 1025;

        if (empty($host)) {
            $this->lastError = "MailHog host is required";
            return false;
        }

        if (!is_numeric($port) || $port <= 0) {
            $this->lastError = "MailHog port must be a valid positive number";
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
     * Build email headers
     *
     * @param string $fromEmail From email address
     * @param string $fromName From name
     * @param bool $isHtml Whether the email is HTML
     * @param array $options Additional options
     * @return array Email headers
     */
    private function buildHeaders(string $fromEmail, string $fromName, bool $isHtml, array $options): array
    {
        $headers = [
            'From' => "{$fromName} <{$fromEmail}>",
            'Reply-To' => $options['reply_to'] ?? $fromEmail,
            'X-Mailer' => 'Phalcon Stub Email Service',
            'MIME-Version' => '1.0',
        ];

        if ($isHtml) {
            $headers['Content-Type'] = 'text/html; charset=UTF-8';
        } else {
            $headers['Content-Type'] = 'text/plain; charset=UTF-8';
        }

        // Add CC and BCC if provided
        if (!empty($options['cc'])) {
            $headers['Cc'] = is_array($options['cc']) ? implode(', ', $options['cc']) : $options['cc'];
        }

        if (!empty($options['bcc'])) {
            $headers['Bcc'] = is_array($options['bcc']) ? implode(', ', $options['bcc']) : $options['bcc'];
        }

        return $headers;
    }

    /**
     * Send email via SMTP to MailHog
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param array $headers Email headers
     * @param string $fromEmail From email address
     * @return bool Success status
     */
    private function sendViaSMTP(string $to, string $subject, string $body, array $headers, string $fromEmail): bool
    {
        try {
            $host = $this->config['host'] ?? 'mailhog';
            $port = $this->config['port'] ?? 1025;

            // Create socket connection to MailHog
            $socket = fsockopen($host, $port, $errno, $errstr, 30);
            if (!$socket) {
                $this->lastError = "Could not connect to MailHog: {$errstr} ({$errno})";
                return false;
            }

            // Read initial response
            $response = fgets($socket, 512);
            if (substr($response, 0, 3) !== '220') {
                $this->lastError = "Invalid SMTP response: {$response}";
                fclose($socket);
                return false;
            }

            // SMTP conversation
            $commands = [
                "HELO localhost\r\n",
                "MAIL FROM: <{$fromEmail}>\r\n",
                "RCPT TO: <{$to}>\r\n",
                "DATA\r\n"
            ];

            foreach ($commands as $command) {
                fputs($socket, $command);
                $response = fgets($socket, 512);

                // Check for SMTP errors
                $code = substr($response, 0, 3);
                if (!in_array($code, ['220', '250', '354'])) {
                    $this->lastError = "SMTP error: {$response}";
                    fclose($socket);
                    return false;
                }
            }

            // Send email data
            $emailData = $this->formatEmailData($to, $subject, $body, $headers);
            fputs($socket, $emailData);
            fputs($socket, "\r\n.\r\n");

            // Read final response
            $response = fgets($socket, 512);
            fclose($socket);

            if (substr($response, 0, 3) !== '250') {
                $this->lastError = "Email not accepted: {$response}";
                return false;
            }

            return true;
        } catch (Exception $e) {
            $this->lastError = "SMTP error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Format email data for SMTP transmission
     *
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $body Email body
     * @param array $headers Email headers
     * @return string Formatted email data
     */
    private function formatEmailData(string $to, string $subject, string $body, array $headers): string
    {
        $data = "To: {$to}\r\n";
        $data .= "Subject: {$subject}\r\n";

        foreach ($headers as $key => $value) {
            $data .= "{$key}: {$value}\r\n";
        }

        $data .= "\r\n{$body}\r\n";

        return $data;
    }
}
