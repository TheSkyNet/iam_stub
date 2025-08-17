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
     *
     * @param array $payload
     * @return bool|string
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
            error_log("Email sent to {$to} with subject: {$subject}");

            return true;

        } catch (Exception $e) {
            return 'Failed to send email: ' . $e->getMessage();
        }
    }

    /**
     * Simulate email sending
     *
     * @param string $to
     * @param string $subject
     * @param string $message
     * @param string $from
     * @throws Exception
     */
    protected function sendEmail(string $to, string $subject, string $message, string $from): void
    {
        // Validate email format
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address: {$to}");
        }

        // Simulate processing time
        sleep(1);

        // In a real implementation, you would use the email service here
        // For example:
        // $emailService = new EmailService();
        // $emailService->send($to, $subject, $message, $from);

        // For demonstration, we'll just log it
        $logMessage = "SIMULATED EMAIL SEND:\n";
        $logMessage .= "To: {$to}\n";
        $logMessage .= "From: {$from}\n";
        $logMessage .= "Subject: {$subject}\n";
        $logMessage .= "Message: {$message}\n";
        $logMessage .= "Sent at: " . date('Y-m-d H:i:s') . "\n";
        
        error_log($logMessage);
    }
}