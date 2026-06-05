<?php

namespace IamLab\Core\Email\Providers;

use IamLab\Core\Email\EmailProviderInterface;
use Exception;

class SystemProvider implements EmailProviderInterface
{
    private array $config;
    private ?string $lastError = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Send an email using PHP's mail() function
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool
    {
        $fromEmail = $options['from_email'] ?? $this->config['from_email'] ?? 'noreply@example.com';
        $fromName = $options['from_name'] ?? $this->config['from_name'] ?? 'Phalcon Stub';

        $isHtml = !isset($options['is_html']) || $options['is_html'] === true;

        $headers = [
            'From' => "$fromName <$fromEmail>",
            'Reply-To' => $options['reply_to'] ?? $fromEmail,
            'X-Mailer' => 'PHP/' . phpversion()
        ];

        if ($isHtml) {
            $headers['MIME-Version'] = '1.0';
            $headers['Content-type'] = 'text/html; charset=UTF-8';
        }

        // Add CC and BCC if provided
        if (!empty($options['cc'])) {
            $headers['Cc'] = is_array($options['cc']) ? implode(', ', $options['cc']) : $options['cc'];
        }

        if (!empty($options['bcc'])) {
            $headers['Bcc'] = is_array($options['bcc']) ? implode(', ', $options['bcc']) : $options['bcc'];
        }

        try {
            $result = mail($to, $subject, $body, $headers);
            if (!$result) {
                $this->lastError = "PHP mail() function returned false";
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
     */
    public function sendBulk(array $recipients, string $subject, string $body, array $options = []): bool
    {
        $success = true;
        foreach ($recipients as $recipient) {
            if (!$this->send($recipient, $subject, $body, $options)) {
                $success = false;
            }
        }
        return $success;
    }

    /**
     * Validate email configuration
     */
    public function validateConfig(): bool
    {
        return true; // System provider usually doesn't need specific config
    }

    /**
     * Get the last error message
     */
    public function getLastError(): ?string
    {
        return $this->lastError;
    }
}
