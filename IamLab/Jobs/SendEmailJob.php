<?php

namespace IamLab\Jobs;

use Exception;

/**
 * Send Email Job
 *
 * Example job for sending emails
 */
class SendEmailJob
{
    /**
     * Handle the job
     */
    public function handle(array $payload): bool|string
    {
        try {
            // Validate required payload fields
            if (!isset($payload['to']) || !isset($payload['subject']) || !isset($payload['message'])) {
                return 'Missing required fields: to, subject, message';
            }

            $to = $payload['to'];
            $subject = $payload['subject'];
            $message = $payload['message'];
            $from = $payload['from'] ?? 'noreply@example.com';

            // Simulate email sending (replace with actual email service)
            $this->sendEmail($to, $subject, $message, $from);

            // Log the email sending
            error_log(sprintf('Email sent to %s with subject: %s', $to, $subject));

            return true;
        } catch (Exception $exception) {
            return 'Failed to send email: ' . $exception->getMessage();
        }
    }

    /**
     * Simulate email sending
     *
     * @throws Exception
     */
    protected function sendEmail(string $to, string $subject, string $message, string $from): void
    {
        // Validate email format
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email address: ' . $to);
        }

        // Simulate processing time
        sleep(1);

        // In a real implementation, you would use the email service here
        // For example:
        // $emailService = new EmailService();
        // $emailService->send($to, $subject, $message, $from);

        // For demonstration, we'll just log it
        $logMessage = "SIMULATED EMAIL SEND:\n";
        $logMessage .= sprintf('To: %s%s', $to, PHP_EOL);
        $logMessage .= sprintf('From: %s%s', $from, PHP_EOL);
        $logMessage .= sprintf('Subject: %s%s', $subject, PHP_EOL);
        $logMessage .= sprintf('Message: %s%s', $message, PHP_EOL);
        $logMessage .= "Sent at: " . date('Y-m-d H:i:s') . "\n";

        error_log($logMessage);
    }
}
