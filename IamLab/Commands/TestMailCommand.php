<?php

namespace IamLab\Commands;

use IamLab\Core\Command\BaseCommand;
use function App\Core\Helpers\email;

class TestMailCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'test:mail [recipient] [--subject=] [--message=] [-d|--debug] [-v|--verbose]';
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'Send a test email to verify email functionality';
    }

    /**
     * Get command help text
     *
     * @return string
     */
    public function getHelp(): string
    {
        return <<<HELP
Send a test email to verify email functionality

Usage:
  test:mail [recipient] [options]

Arguments:
  recipient             Email address to send test email to (optional, will prompt if not provided)

Options:
  --subject=SUBJECT     Email subject (default: "Test Email from Phalcon Stub")
  --message=MESSAGE     Email message (default: auto-generated message)
  -d, --debug          Enable debug output
  -v, --verbose        Enable verbose output

Examples:
  ./phalcons command test:mail user@example.com
  ./phalcons command test:mail user@example.com --subject="Custom Subject" -v
  ./phalcons command test:mail --debug
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    protected function handle(): int
    {
        $this->info("Starting email test...");

        // Get recipient email
        $recipient = $this->argument(0);
        if (!$recipient) {
            $recipient = $this->ask("Enter recipient email address");
        }

        if (!$recipient || !filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email address provided");
            return 1;
        }

        $this->verbose("Recipient: {$recipient}");

        // Get email subject
        $subject = $this->option('subject', 'Test Email from Phalcon Stub');
        $this->verbose("Subject: {$subject}");

        // Get email message
        $message = $this->option('message');
        if (!$message) {
            $message = $this->generateTestMessage();
        }
        $this->verbose("Message length: " . strlen($message) . " characters");

        // Send the email
        $this->info("Sending test email...");
        
        try {
            $result = email(
                $recipient,
                $subject,
                $message,
                [
                    'is_html' => true,
                    'from_name' => 'Phalcon Stub Command'
                ]
            );

            if ($result) {
                $this->success("Test email sent successfully to {$recipient}");
                $this->info("Check your email client or MailHog dashboard (http://localhost:8025) to view the email");
                return 0;
            } else {
                $this->error("Failed to send test email");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Exception occurred while sending email: " . $e->getMessage());
            $this->debug("Stack trace: " . $e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Generate a test email message
     *
     * @return string
     */
    private function generateTestMessage(): string
    {
        $timestamp = date('Y-m-d H:i:s');
        
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <title>Test Email</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #8198c4; color: white; padding: 20px; text-align: center; }
        .content { padding: 20px; background: #f9f9f9; }
        .footer { padding: 10px; text-align: center; font-size: 12px; color: #666; }
        .success { color: #28a745; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Phalcon Stub Test Email</h1>
        </div>
        <div class="content">
            <h2>Email Service Test</h2>
            <p class="success">✅ Congratulations! Your email service is working correctly.</p>
            
            <h3>Test Details:</h3>
            <ul>
                <li><strong>Sent at:</strong> {$timestamp}</li>
                <li><strong>Command:</strong> test:mail</li>
                <li><strong>Framework:</strong> Phalcon PHP</li>
                <li><strong>Email Service:</strong> Multi-provider (MailHog/Resend)</li>
            </ul>
            
            <h3>What's Working:</h3>
            <ul>
                <li>✅ Email service initialization</li>
                <li>✅ HTML email formatting</li>
                <li>✅ Command-line email sending</li>
                <li>✅ Configuration loading</li>
            </ul>
            
            <p>This email was sent using the Phalcon Stub command runner system. You can now use the email service throughout your application for notifications, password resets, and other communications.</p>
        </div>
        <div class="footer">
            <p>Generated by Phalcon Stub Command Runner</p>
            <p>If you received this email in error, please ignore it.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}