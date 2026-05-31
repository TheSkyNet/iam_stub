<?php

namespace IamLab\Commands;

use Exception;
use IamLab\Core\Command\BaseCommand;
use IamLab\Core\Pusher\PusherService;

class TestPusherCommand extends BaseCommand
{
    /**
     * Get command signature/usage
     */
    #[\Override]
    public function getSignature(): string
    {
        return 'test:pusher [channel] [--event=] [--message=] [-d|--debug] [-v|--verbose]';
    }

    /**
     * Get command description
     */
    #[\Override]
    public function getDescription(): string
    {
        return 'Test Pusher real-time functionality';
    }

    /**
     * Get command help text
     */
    #[\Override]
    public function getHelp(): string
    {
        return <<<HELP
Test Pusher real-time functionality

Usage:
  test:pusher [channel] [options]

Arguments:
  channel               Channel name to send test event to (default: "test-channel")

Options:
  --event=EVENT         Event name (default: "test-event")
  --message=MESSAGE     Test message (default: auto-generated message)
  -d, --debug          Enable debug output
  -v, --verbose        Enable verbose output

Examples:
  ./phalcons command test:pusher
  ./phalcons command test:pusher my-channel --event="custom-event" -v
  ./phalcons command test:pusher notifications --message="Hello World" --debug
HELP;
    }

    /**
     * Handle the command execution
     *
     * @return int Exit code
     */
    #[\Override]
    protected function handle(): int
    {
        $this->info("Starting Pusher test...");

        // Initialize Pusher service
        try {
            $pusherService = new PusherService();
        } catch (Exception $exception) {
            $this->error("Failed to initialize Pusher service: " . $exception->getMessage());
            return 1;
        }

        // Check if Pusher is ready
        if (!$pusherService->isReady()) {
            $this->error("Pusher service not ready: " . $pusherService->getLastError());
            $this->info("Make sure to:");
            $this->info("1. Install Pusher package: ./phalcons composer require pusher/pusher-php-server");
            $this->info("2. Configure Pusher credentials in .env file");
            return 1;
        }

        $this->success("Pusher service initialized successfully");

        // Get channel name
        $channel = $this->argument(0, 'test-channel');
        $this->verbose('Channel: ' . $channel);

        // Get event name
        $event = $this->option('event', 'test-event');
        $this->verbose('Event: ' . $event);

        // Get test message
        $message = $this->option('message');
        if (!$message) {
            $message = $this->generateTestMessage();
        }

        $this->verbose('Message: ' . $message);

        // Prepare event data
        $eventData = [
            'message' => $message,
            'timestamp' => time(),
            'datetime' => date('Y-m-d H:i:s'),
            'command' => 'test:pusher',
            'channel' => $channel,
            'event' => $event,
            'test_id' => uniqid('test_', true)
        ];

        $this->debug("Event data: " . json_encode($eventData, JSON_PRETTY_PRINT));

        // Send the event
        $this->info("Sending test event to Pusher...");

        try {
            $result = $pusherService->trigger($channel, $event, $eventData);

            if ($result) {
                $this->success("Test event sent successfully!");
                $this->info('Channel: ' . $channel);
                $this->info('Event: ' . $event);
                $this->info('Message: ' . $message);
                $this->info("");
                $this->info("To see the event in action:");
                $this->info("1. Open your browser to: http://localhost:8080/pusher-test");
                $this->info(sprintf("2. Subscribe to channel '%s' and listen for event '%s'", $channel, $event));
                $this->info("3. Or check your Pusher dashboard for event logs");

                // Test channel info if verbose
                if ($this->verbose) {
                    $this->testChannelInfo($pusherService, $channel);
                }

                return 0;
            }

            $this->error("Failed to send test event: " . $pusherService->getLastError());
            return 1;
        } catch (Exception $exception) {
            $this->error("Exception occurred while sending event: " . $exception->getMessage());
            $this->debug("Stack trace: " . $exception->getTraceAsString());
            return 1;
        }
    }

    /**
     * Test channel information retrieval
     */
    private function testChannelInfo(PusherService $pusherService, string $channel): void
    {
        $this->verbose("Testing channel information retrieval...");

        try {
            $channelInfo = $pusherService->getChannelInfo($channel, ['user_count']);

            if ($channelInfo !== false) {
                $this->verbose("Channel info retrieved successfully:");
                $this->verbose("  User count: " . ($channelInfo['user_count'] ?? 'N/A'));
            } else {
                $this->verbose("Could not retrieve channel info: " . $pusherService->getLastError());
            }
        } catch (Exception $exception) {
            $this->verbose("Channel info test failed: " . $exception->getMessage());
        }
    }

    /**
     * Generate a test message
     */
    private function generateTestMessage(): string
    {
        $messages = [
            "Hello from Phalcon Stub command runner!",
            "Testing real-time communication with Pusher.js",
            "This is a test message sent via command line",
            "Real-time events are working correctly!",
            "Pusher integration test successful",
            "Command runner -> Pusher -> Frontend pipeline active"
        ];

        return $messages[array_rand($messages)];
    }
}
