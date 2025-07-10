<?php

namespace IamLab\Core\Pusher;

use GuzzleHttp\Exception\GuzzleException;
use Phalcon\Di\Injectable;
use Exception;
use Pusher\Pusher;

class PusherService extends Injectable
{
    private ?Pusher $pusher = null;
    private array $config;
    private string $lastError = '';

    public function __construct()
    {
        $this->config = $this->getDI()->getShared('config')->pusher->toArray();
        $this->initializePusher();
    }

    /**
     * Initialize Pusher client
     */
    private function initializePusher(): void
    {
        try {
            // Check if Pusher class exists (package installed)
            if (!class_exists('\Pusher\Pusher')) {
                throw new Exception("Pusher PHP package not installed. Run: composer require pusher/pusher-php-server");
            }

            // Build options array, only including host and port if they're not empty
            $options = [
                'cluster' => $this->config['cluster'],
                'useTLS' => $this->config['use_tls'] ?? true,
                'scheme' => $this->config['scheme'] ?? 'https',
                'curl_options' => [
                    CURLOPT_SSL_VERIFYHOST => $this->config['verify_ssl'] ?? 2,
                    CURLOPT_SSL_VERIFYPEER => $this->config['verify_ssl'] ?? true,
                ]
            ];

            // Only add host and port if they're not empty
            if (!empty($this->config['host'])) {
                $options['host'] = $this->config['host'];
            }
            if (!empty($this->config['port'])) {
                $options['port'] = $this->config['port'];
            }

            $this->pusher = new Pusher(
                $this->config['key'],
                $this->config['secret'],
                $this->config['app_id'],
                $options
            );
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            error_log("Pusher service initialization failed: " . $e->getMessage());
        }
    }

    /**
     * Trigger an event on a channel
     *
     * @param string $channel Channel name
     * @param string $event Event name
     * @param array $data Event data
     * @param array $options Additional options
     * @return bool Success status
     *
     * @throws GuzzleException
     */
    public function trigger(string $channel, string $event, array $data = [], array $options = []): bool
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            $result = $this->pusher->trigger($channel, $event, $data, $options);
            return $result != false;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Trigger an event on multiple channels
     *
     * @param array $channels Array of channel names
     * @param string $event Event name
     * @param array $data Event data
     * @param array $options Additional options
     * @return bool Success status
     *
     * @throws GuzzleException
     */
    public function triggerBatch(array $channels, string $event, array $data = [], array $options = []): bool
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            $result = $this->pusher->trigger($channels, $event, $data, $options);
            return $result !== false;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Get channel information
     *
     * @param string $channel Channel name
     * @param array $info Info to retrieve
     * @return array|false Channel info or false on failure
     */
    public function getChannelInfo(string $channel, array $info = []): array|false
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            $result = $this->pusher->getChannelInfo($channel, $info);
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Get list of channels
     *
     * @param array $options Options for filtering
     * @return array|false Channels list or false on failure
     */
    public function getChannels(array $options = []): array|false
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            $result = $this->pusher->getChannels($options);
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Authenticate private channel
     *
     * @param string $channel Channel name
     * @param string $socketId Socket ID
     * @param array $customData Custom data for presence channels
     * @return string|false Auth signature or false on failure
     */
    public function authenticateChannel(string $channel, string $socketId, array $customData = []): string|false
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            if (str_starts_with($channel, 'presence-')) {
                // Presence channel
                $result = $this->pusher->presenceAuth($channel, $socketId, null, $customData);
            } else {
                // Private channel
                $result = $this->pusher->socketAuth($channel, $socketId);
            }
            return $result;
        } catch (Exception $e) {
            $this->lastError = $e->getMessage();
            return false;
        }
    }

    /**
     * Send webhook verification
     *
     * @param array $headers Request headers
     * @param string $body Request body
     * @return bool Verification result
     */
    public function verifyWebhook(array $headers, string $body): bool
    {
        if (!$this->pusher) {
            $this->lastError = "Pusher not initialized";
            return false;
        }

        try {
            $webhook = $this->pusher->webhook($headers, $body);
            return $webhook->isValid();
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
     * Check if Pusher is ready
     *
     * @return bool True if Pusher is initialized and ready
     */
    public function isReady(): bool
    {
        return $this->pusher !== null;
    }

    /**
     * Get Pusher configuration
     *
     * @return array Configuration array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get client configuration for frontend
     *
     * @return array Client configuration
     */
    public function getClientConfig(): array
    {
        return [
            'key' => $this->config['key'],
            'cluster' => $this->config['cluster'],
            'forceTLS' => $this->config['use_tls'] ?? true,
            'host' => !empty($this->config['host']) ? $this->config['host'] : null,
            'port' => !empty($this->config['port']) ? $this->config['port'] : null,
            'disableStats' => $this->config['disable_stats'] ?? false,
            'enabledTransports' => $this->config['enabled_transports'] ?? ['ws', 'wss'],
        ];
    }
}
