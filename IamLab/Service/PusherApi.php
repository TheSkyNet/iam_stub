<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Core\Pusher\PusherService;
use IamLab\Service\Auth\AuthService;

class PusherApi extends aAPI
{
    protected array $skipCsrfActions = ['webhook'];

    /**
     * Get Pusher configuration for frontend
     */
    public function configAction(): void
    {
        try {
            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available: ' . $pusherService->getLastError()
                ]);
            }

            $clientConfig = $pusherService->getClientConfig();

            $this->dispatch([
                'success' => true,
                'data' => $clientConfig
            ]);
        } catch (Exception $exception) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get Pusher configuration',
                'debug' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Authenticate private/presence channels
     */
    public function authAction(): void
    {
        try {
            // Check if user is authenticated
            $authService = new AuthService();
            if (!$authService->isAuthenticated()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]);
            }

            // Get required parameters
            $socketId = $this->getParam('socket_id');
            $channelName = $this->getParam('channel_name');

            if (empty($socketId) || empty($channelName)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing socket_id or channel_name'
                ]);
            }

            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
            }

            // Get user data for presence channels
            $customData = [];
            if (str_starts_with((string) $channelName, 'presence-')) {
                $user = $authService->getUser();
                $customData = [
                    'user_id' => $user->getId(),
                    'user_info' => [
                        'name' => $user->getName(),
                        'email' => $user->getEmail()
                    ]
                ];
            }

            // Authenticate the channel
            $authSignature = $pusherService->authenticateChannel($channelName, $socketId, $customData);

            if ($authSignature === false) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Failed to authenticate channel: ' . $pusherService->getLastError()
                ]);
            }

            // Return the authentication signature
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setContent($authSignature);
            $this->response->send();
        } catch (Exception $exception) {
            $this->dispatch([
                'success' => false,
                'message' => 'Authentication failed',
                'debug' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Trigger an event (for testing purposes)
     */
    public function triggerAction(): void
    {
        try {
            // Check if user is authenticated
            $authService = new AuthService();
            if (!$authService->isAuthenticated()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]);
            }

            // Get parameters
            $channel = $this->getParam('channel');
            $event = $this->getParam('event');
            $data = $this->getParam('data', []);

            if (empty($channel) || empty($event)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing channel or event parameter'
                ]);
            }

            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
            }

            // Trigger the event
            $result = $pusherService->trigger($channel, $event, $data);

            if ($result) {
                $this->dispatch([
                    'success' => true,
                    'message' => 'Event triggered successfully'
                ]);
            } else {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Failed to trigger event: ' . $pusherService->getLastError()
                ]);
            }
        } catch (Exception $exception) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to trigger event',
                'debug' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Get channel information
     */
    public function channelInfoAction(): void
    {
        try {
            // Check if user is authenticated
            $authService = new AuthService();
            if (!$authService->isAuthenticated()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]);
            }

            $channel = $this->getParam('channel');
            $info = $this->getParam('info', ['user_count']);

            if (empty($channel)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing channel parameter'
                ]);
            }

            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
            }

            $channelInfo = $pusherService->getChannelInfo($channel, $info);

            if ($channelInfo !== false) {
                $this->dispatch([
                    'success' => true,
                    'data' => $channelInfo
                ]);
            } else {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Failed to get channel info: ' . $pusherService->getLastError()
                ]);
            }
        } catch (Exception $exception) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get channel info',
                'debug' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Get list of channels
     */
    public function channelsAction(): void
    {
        try {
            // Check if user is authenticated
            $authService = new AuthService();
            if (!$authService->isAuthenticated()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'User not authenticated'
                ]);
            }

            $options = $this->getParam('options', []);

            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
            }

            $channels = $pusherService->getChannels($options);

            if ($channels !== false) {
                $this->dispatch([
                    'success' => true,
                    'data' => $channels
                ]);
            } else {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Failed to get channels: ' . $pusherService->getLastError()
                ]);
            }
        } catch (Exception $exception) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get channels',
                'debug' => $exception->getMessage()
            ]);
        }
    }

    /**
     * Handle Pusher webhooks
     */
    public function webhookAction(): void
    {
        try {
            $headers = getallheaders();
            $body = $this->request->getRawBody();

            $pusherService = new PusherService();

            if (!$pusherService->isReady()) {
                $this->response->setStatusCode(500);
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
            }

            $isValid = $pusherService->verifyWebhook($headers, $body);

            if ($isValid) {
                // Process webhook data
                $webhookData = json_decode((string) $body, true);

                // Log webhook for debugging
                error_log("Pusher webhook received: " . $body);

                // Here you can add custom webhook processing logic
                // For example, logging events, updating database, etc.

                $this->response->setStatusCode(200);
                $this->dispatch([
                    'success' => true,
                    'message' => 'Webhook processed successfully'
                ]);
            } else {
                $this->response->setStatusCode(401);
                $this->dispatch([
                    'success' => false,
                    'message' => 'Invalid webhook signature'
                ]);
            }
        } catch (Exception $exception) {
            $this->response->setStatusCode(500);
            $this->dispatch([
                'success' => false,
                'message' => 'Webhook processing failed',
                'debug' => $exception->getMessage()
            ]);
        }
    }
}
