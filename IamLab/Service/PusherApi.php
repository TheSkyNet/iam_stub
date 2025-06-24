<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Core\Pusher\PusherService;
use IamLab\Service\Auth\AuthService;

class PusherApi extends aAPI
{
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
                return;
            }

            $clientConfig = $pusherService->getClientConfig();
            
            $this->dispatch([
                'success' => true,
                'data' => $clientConfig
            ]);

        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get Pusher configuration',
                'debug' => $e->getMessage()
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
                return;
            }

            // Get required parameters
            $socketId = $this->getParam('socket_id');
            $channelName = $this->getParam('channel_name');

            if (empty($socketId) || empty($channelName)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing socket_id or channel_name'
                ]);
                return;
            }

            $pusherService = new PusherService();
            
            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
                return;
            }

            // Get user data for presence channels
            $customData = [];
            if (str_starts_with($channelName, 'presence-')) {
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
                return;
            }

            // Return the authentication signature
            $this->response->setContentType('application/json', 'UTF-8');
            $this->response->setContent($authSignature);
            $this->response->send();

        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Authentication failed',
                'debug' => $e->getMessage()
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
                return;
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
                return;
            }

            $pusherService = new PusherService();
            
            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
                return;
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

        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to trigger event',
                'debug' => $e->getMessage()
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
                return;
            }

            $channel = $this->getParam('channel');
            $info = $this->getParam('info', ['user_count']);

            if (empty($channel)) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Missing channel parameter'
                ]);
                return;
            }

            $pusherService = new PusherService();
            
            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
                return;
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

        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get channel info',
                'debug' => $e->getMessage()
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
                return;
            }

            $options = $this->getParam('options', []);

            $pusherService = new PusherService();
            
            if (!$pusherService->isReady()) {
                $this->dispatch([
                    'success' => false,
                    'message' => 'Pusher service not available'
                ]);
                return;
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

        } catch (Exception $e) {
            $this->dispatch([
                'success' => false,
                'message' => 'Failed to get channels',
                'debug' => $e->getMessage()
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
                return;
            }

            $isValid = $pusherService->verifyWebhook($headers, $body);

            if ($isValid) {
                // Process webhook data
                $webhookData = json_decode($body, true);
                
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

        } catch (Exception $e) {
            $this->response->setStatusCode(500);
            $this->dispatch([
                'success' => false,
                'message' => 'Webhook processing failed',
                'debug' => $e->getMessage()
            ]);
        }
    }
}