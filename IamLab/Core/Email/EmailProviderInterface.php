<?php

namespace IamLab\Core\Email;

interface EmailProviderInterface
{
    /**
     * Send an email
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $options Additional options (cc, bcc, attachments, etc.)
     * @return bool True if email was sent successfully
     */
    public function send(string $to, string $subject, string $body, array $options = []): bool;

    /**
     * Send an email to multiple recipients
     *
     * @param array $recipients Array of recipient email addresses
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $options Additional options (cc, bcc, attachments, etc.)
     * @return bool True if email was sent successfully
     */
    public function sendBulk(array $recipients, string $subject, string $body, array $options = []): bool;

    /**
     * Validate email configuration
     *
     * @return bool True if configuration is valid
     */
    public function validateConfig(): bool;

    /**
     * Get the last error message
     *
     * @return string|null Last error message or null if no error
     */
    public function getLastError(): ?string;
}