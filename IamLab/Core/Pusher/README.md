# Pusher Service

The Pusher Service provides real-time WebSocket communication capabilities for the Phalcon stub project. It integrates with Pusher.js to enable live updates, real-time messaging, notifications, and interactive features in web applications.

## Overview

This service wraps the Pusher PHP SDK to provide a clean, easy-to-use interface for triggering events, managing channels, and handling real-time communication between the server and client applications.

## Components

### PusherService Class

**Namespace:** `IamLab\Core\Pusher`

**File:** `PusherService.php`

The main service class that handles all Pusher operations including event triggering, channel management, authentication, and webhook verification.

#### Key Features

- **Event Triggering**: Send real-time events to connected clients
- **Channel Management**: Handle public, private, and presence channels
- **Authentication**: Authenticate private and presence channels
- **Webhook Verification**: Verify incoming Pusher webhooks
- **Error Handling**: Comprehensive error handling and logging
- **Configuration Management**: Flexible configuration from environment variables

## Configuration

### Environment Variables

Add these variables to your `.env` file:

```env
# Pusher Configuration
PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=your_cluster
PUSHER_USE_TLS=true
PUSHER_HOST=
PUSHER_PORT=
PUSHER_SCHEME=https
PUSHER_VERIFY_SSL=true
PUSHER_DISABLE_STATS=false
```

### Configuration File

The service reads configuration from `config/config.php`:

```php
'pusher' => [
    'app_id' => App\Core\Helpers\env('PUSHER_APP_ID', ''),
    'key' => App\Core\Helpers\env('PUSHER_APP_KEY', ''),
    'secret' => App\Core\Helpers\env('PUSHER_APP_SECRET', ''),
    'cluster' => App\Core\Helpers\env('PUSHER_APP_CLUSTER', 'mt1'),
    'use_tls' => App\Core\Helpers\env('PUSHER_USE_TLS', true),
    'host' => App\Core\Helpers\env('PUSHER_HOST', null),
    'port' => App\Core\Helpers\env('PUSHER_PORT', null),
    'scheme' => App\Core\Helpers\env('PUSHER_SCHEME', 'https'),
    'verify_ssl' => App\Core\Helpers\env('PUSHER_VERIFY_SSL', true),
    'disable_stats' => App\Core\Helpers\env('PUSHER_DISABLE_STATS', false),
    'enabled_transports' => ['ws', 'wss'],
]
```

## API Reference

### Constructor

```php
public function __construct()
```

Initializes the Pusher service with configuration from the DI container.

### Core Methods

#### `trigger(string $channel, string $event, array $data = [], array $options = []): bool`

Triggers an event on a specific channel.

**Parameters:**
- `$channel` - Channel name
- `$event` - Event name
- `$data` - Event data (array)
- `$options` - Additional options

**Returns:** `bool` - Success status

**Example:**
```php
$pusherService = new PusherService();

// Simple event
$success = $pusherService->trigger(
    'notifications',
    'new-message',
    ['message' => 'Hello World!', 'user' => 'John']
);

// Event with options
$success = $pusherService->trigger(
    'user-123',
    'profile-updated',
    ['name' => 'John Doe', 'email' => 'john@example.com'],
    ['socket_id' => '123.456'] // Exclude sender
);
```

#### `triggerBatch(array $channels, string $event, array $data = [], array $options = []): bool`

Triggers an event on multiple channels simultaneously.

**Parameters:**
- `$channels` - Array of channel names
- `$event` - Event name
- `$data` - Event data (array)
- `$options` - Additional options

**Returns:** `bool` - Success status

**Example:**
```php
// Broadcast to multiple channels
$success = $pusherService->triggerBatch(
    ['notifications', 'admin-alerts', 'user-updates'],
    'system-announcement',
    [
        'title' => 'Maintenance Notice',
        'message' => 'System will be down for maintenance',
        'timestamp' => time()
    ]
);
```

#### `getChannelInfo(string $channel, array $info = []): array|false`

Retrieves information about a specific channel.

**Parameters:**
- `$channel` - Channel name
- `$info` - Array of info types to retrieve

**Returns:** `array|false` - Channel information or false on failure

**Example:**
```php
// Get user count for a channel
$info = $pusherService->getChannelInfo('chat-room', ['user_count']);
echo "Users online: " . $info['user_count'];

// Get subscription count for presence channel
$info = $pusherService->getChannelInfo('presence-lobby', ['subscription_count']);
```

#### `getChannels(array $options = []): array|false`

Retrieves a list of active channels.

**Parameters:**
- `$options` - Filtering options

**Returns:** `array|false` - Channels list or false on failure

**Example:**
```php
// Get all channels
$channels = $pusherService->getChannels();

// Get channels with prefix filter
$chatChannels = $pusherService->getChannels([
    'filter_by_prefix' => 'chat-'
]);
```

#### `authenticateChannel(string $channel, string $socketId, array $customData = []): string|false`

Authenticates a private or presence channel.

**Parameters:**
- `$channel` - Channel name
- `$socketId` - Client socket ID
- `$customData` - Custom data for presence channels

**Returns:** `string|false` - Authentication signature or false on failure

**Example:**
```php
// Authenticate private channel
$auth = $pusherService->authenticateChannel(
    'private-user-123',
    $socketId
);

// Authenticate presence channel with user data
$auth = $pusherService->authenticateChannel(
    'presence-chat-room',
    $socketId,
    [
        'user_id' => 123,
        'user_info' => [
            'name' => 'John Doe',
            'avatar' => 'avatar.jpg'
        ]
    ]
);
```

#### `verifyWebhook(array $headers, string $body): bool`

Verifies the authenticity of a Pusher webhook.

**Parameters:**
- `$headers` - Request headers
- `$body` - Request body

**Returns:** `bool` - Verification result

**Example:**
```php
// In webhook handler
$headers = getallheaders();
$body = file_get_contents('php://input');

if ($pusherService->verifyWebhook($headers, $body)) {
    // Process webhook
    $data = json_decode($body, true);
    // Handle webhook events
} else {
    // Invalid webhook
    http_response_code(401);
}
```

### Utility Methods

#### `isReady(): bool`

Checks if the Pusher service is properly initialized.

#### `getLastError(): string`

Returns the last error message.

#### `getConfig(): array`

Returns the current Pusher configuration.

#### `getClientConfig(): array`

Returns configuration suitable for frontend clients.

## Usage Examples

### Basic Real-time Notifications

```php
<?php

use IamLab\Core\Pusher\PusherService;

class NotificationService
{
    private PusherService $pusher;

    public function __construct()
    {
        $this->pusher = new PusherService();
    }

    public function sendUserNotification($userId, $message, $type = 'info')
    {
        return $this->pusher->trigger(
            "user-{$userId}",
            'notification',
            [
                'message' => $message,
                'type' => $type,
                'timestamp' => time(),
                'id' => uniqid()
            ]
        );
    }

    public function broadcastSystemMessage($message)
    {
        return $this->pusher->trigger(
            'system',
            'announcement',
            [
                'message' => $message,
                'timestamp' => time(),
                'priority' => 'high'
            ]
        );
    }
}

// Usage
$notificationService = new NotificationService();

// Send to specific user
$notificationService->sendUserNotification(
    123,
    'Your order has been shipped!',
    'success'
);

// Broadcast to all users
$notificationService->broadcastSystemMessage(
    'System maintenance scheduled for tonight'
);
```

### Real-time Chat System

```php
<?php

use IamLab\Core\Pusher\PusherService;

class ChatService
{
    private PusherService $pusher;

    public function __construct()
    {
        $this->pusher = new PusherService();
    }

    public function sendMessage($roomId, $userId, $message, $userName)
    {
        $messageData = [
            'id' => uniqid(),
            'user_id' => $userId,
            'user_name' => $userName,
            'message' => $message,
            'timestamp' => time(),
            'room_id' => $roomId
        ];

        // Store message in database
        $this->storeMessage($messageData);

        // Broadcast to room
        return $this->pusher->trigger(
            "chat-room-{$roomId}",
            'new-message',
            $messageData
        );
    }

    public function userJoinedRoom($roomId, $userId, $userName)
    {
        return $this->pusher->trigger(
            "chat-room-{$roomId}",
            'user-joined',
            [
                'user_id' => $userId,
                'user_name' => $userName,
                'timestamp' => time()
            ]
        );
    }

    public function userLeftRoom($roomId, $userId, $userName)
    {
        return $this->pusher->trigger(
            "chat-room-{$roomId}",
            'user-left',
            [
                'user_id' => $userId,
                'user_name' => $userName,
                'timestamp' => time()
            ]
        );
    }

    private function storeMessage($messageData)
    {
        // Store message in database
        // Implementation depends on your data layer
    }
}
```

### Live Data Updates

```php
<?php

use IamLab\Core\Pusher\PusherService;

class LiveDataService
{
    private PusherService $pusher;

    public function __construct()
    {
        $this->pusher = new PusherService();
    }

    public function updateDashboardMetrics($metrics)
    {
        return $this->pusher->trigger(
            'dashboard',
            'metrics-update',
            [
                'metrics' => $metrics,
                'timestamp' => time()
            ]
        );
    }

    public function updateOrderStatus($orderId, $status, $customerId = null)
    {
        $channels = ["order-{$orderId}"];
        
        // Also notify customer if provided
        if ($customerId) {
            $channels[] = "customer-{$customerId}";
        }

        return $this->pusher->triggerBatch(
            $channels,
            'order-status-changed',
            [
                'order_id' => $orderId,
                'status' => $status,
                'timestamp' => time()
            ]
        );
    }

    public function updateInventory($productId, $quantity)
    {
        return $this->pusher->trigger(
            'inventory',
            'stock-update',
            [
                'product_id' => $productId,
                'quantity' => $quantity,
                'timestamp' => time()
            ]
        );
    }
}
```

### Presence Channels for User Activity

```php
<?php

use IamLab\Core\Pusher\PusherService;

class PresenceService
{
    private PusherService $pusher;

    public function __construct()
    {
        $this->pusher = new PusherService();
    }

    public function authenticatePresenceChannel($channelName, $socketId, $user)
    {
        // Only allow authenticated users
        if (!$user) {
            return false;
        }

        $userData = [
            'user_id' => $user->getId(),
            'user_info' => [
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                'avatar' => $user->getAvatar(),
                'status' => 'online'
            ]
        ];

        return $this->pusher->authenticateChannel(
            $channelName,
            $socketId,
            $userData
        );
    }

    public function broadcastUserActivity($userId, $activity)
    {
        return $this->pusher->trigger(
            'presence-activity',
            'user-activity',
            [
                'user_id' => $userId,
                'activity' => $activity,
                'timestamp' => time()
            ]
        );
    }
}
```

### Webhook Handler

```php
<?php

use IamLab\Core\Pusher\PusherService;

class PusherWebhookHandler
{
    private PusherService $pusher;

    public function __construct()
    {
        $this->pusher = new PusherService();
    }

    public function handleWebhook()
    {
        $headers = getallheaders();
        $body = file_get_contents('php://input');

        // Verify webhook authenticity
        if (!$this->pusher->verifyWebhook($headers, $body)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid webhook signature']);
            return;
        }

        $data = json_decode($body, true);

        foreach ($data['events'] as $event) {
            $this->processWebhookEvent($event);
        }

        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }

    private function processWebhookEvent($event)
    {
        switch ($event['name']) {
            case 'channel_occupied':
                $this->handleChannelOccupied($event);
                break;
            case 'channel_vacated':
                $this->handleChannelVacated($event);
                break;
            case 'member_added':
                $this->handleMemberAdded($event);
                break;
            case 'member_removed':
                $this->handleMemberRemoved($event);
                break;
        }
    }

    private function handleChannelOccupied($event)
    {
        // Log channel activity
        error_log("Channel occupied: " . $event['channel']);
    }

    private function handleChannelVacated($event)
    {
        // Log channel activity
        error_log("Channel vacated: " . $event['channel']);
    }

    private function handleMemberAdded($event)
    {
        // Handle user joining presence channel
        error_log("Member added to " . $event['channel'] . ": " . $event['user_id']);
    }

    private function handleMemberRemoved($event)
    {
        // Handle user leaving presence channel
        error_log("Member removed from " . $event['channel'] . ": " . $event['user_id']);
    }
}
```

## Frontend Integration

### JavaScript Client Setup

```javascript
// Initialize Pusher client
const pusher = new Pusher('your-app-key', {
    cluster: 'your-cluster',
    forceTLS: true
});

// Subscribe to public channel
const channel = pusher.subscribe('notifications');
channel.bind('new-message', function(data) {
    console.log('New message:', data);
    displayNotification(data.message);
});

// Subscribe to private channel (requires authentication)
const privateChannel = pusher.subscribe('private-user-123');
privateChannel.bind('personal-update', function(data) {
    updateUserInterface(data);
});

// Subscribe to presence channel
const presenceChannel = pusher.subscribe('presence-chat-room');
presenceChannel.bind('pusher:subscription_succeeded', function(members) {
    console.log('Online users:', members.count);
    displayOnlineUsers(members);
});
```

### Authentication Endpoint

The frontend needs an authentication endpoint for private/presence channels:

```php
// In your API routes
$app->post('/api/pusher/auth', function() use ($app) {
    $pusherService = new PusherService();
    $authService = new AuthService();
    
    // Check if user is authenticated
    if (!$authService->isAuthenticated()) {
        return json_encode(['error' => 'Unauthorized']);
    }
    
    $socketId = $_POST['socket_id'];
    $channelName = $_POST['channel_name'];
    $user = $authService->getUser();
    
    // For presence channels, include user data
    $customData = [];
    if (strpos($channelName, 'presence-') === 0) {
        $customData = [
            'user_id' => $user->getId(),
            'user_info' => [
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ]
        ];
    }
    
    $auth = $pusherService->authenticateChannel($channelName, $socketId, $customData);
    
    if ($auth) {
        echo $auth;
    } else {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
    }
});
```

## Testing

### Test Real-time Features

The project includes a Pusher test page at `/pusher-test` that allows you to:

- Check connection status
- Send test messages
- Monitor real-time communication
- Test multi-tab functionality

### Manual Testing

```php
<?php

// Test script
require_once 'vendor/autoload.php';

use IamLab\Core\Pusher\PusherService;

$pusher = new PusherService();

if (!$pusher->isReady()) {
    echo "Pusher not ready: " . $pusher->getLastError() . "\n";
    exit(1);
}

// Test event triggering
$result = $pusher->trigger('test-channel', 'test-event', [
    'message' => 'Hello from PHP!',
    'timestamp' => time()
]);

if ($result) {
    echo "Event triggered successfully!\n";
} else {
    echo "Failed to trigger event: " . $pusher->getLastError() . "\n";
}

// Test channel info
$info = $pusher->getChannelInfo('test-channel', ['user_count']);
if ($info) {
    echo "Channel info: " . json_encode($info) . "\n";
}
```

## Error Handling

### Common Errors and Solutions

1. **"Pusher not initialized"**
   - Check your Pusher credentials in `.env`
   - Ensure the Pusher PHP package is installed: `composer require pusher/pusher-php-server`

2. **"cURL error 35: SSL routines::wrong version number"**
   - Check your `PUSHER_USE_TLS` and `PUSHER_SCHEME` settings
   - Ensure you're using the correct cluster

3. **"Invalid webhook signature"**
   - Verify your webhook secret matches your Pusher app secret
   - Check that the webhook URL is correctly configured in Pusher dashboard

### Error Logging

```php
// Enable error logging
if (!$pusher->isReady()) {
    error_log("Pusher initialization failed: " . $pusher->getLastError());
}

// Log failed events
if (!$pusher->trigger($channel, $event, $data)) {
    error_log("Failed to trigger Pusher event: " . $pusher->getLastError());
}
```

## Security Considerations

1. **Environment Variables**: Keep Pusher credentials secure and never commit them to version control
2. **Channel Authentication**: Always authenticate private and presence channels properly
3. **Webhook Verification**: Always verify webhook signatures to prevent spoofing
4. **Data Sanitization**: Sanitize data before sending through Pusher events
5. **Rate Limiting**: Implement rate limiting to prevent abuse

## Performance Considerations

1. **Batch Operations**: Use `triggerBatch()` for multiple channels instead of multiple `trigger()` calls
2. **Event Size**: Keep event data small to reduce bandwidth usage
3. **Channel Management**: Clean up unused channels and subscriptions
4. **Connection Limits**: Be aware of Pusher connection limits for your plan

## Dependencies

- **pusher/pusher-php-server** - Pusher PHP SDK
- **Phalcon Framework** - For dependency injection and configuration
- **cURL** - For HTTP requests to Pusher API

## Related Documentation

- [Pusher PHP SDK Documentation](https://pusher.com/docs/channels/server_api/php/)
- [Pusher JavaScript SDK Documentation](https://pusher.com/docs/channels/getting_started/javascript/)
- [API Documentation](../API/README.md)
- [Email Documentation](../Email/README.md)
- [Main Project README](../../../README.md)

## Troubleshooting

### Debug Mode

Enable debug mode to see detailed logs:

```php
// In development environment
if (env('APP_DEBUG')) {
    $pusher = new PusherService();
    
    // Log configuration
    error_log("Pusher config: " . json_encode($pusher->getConfig()));
    
    // Log client config
    error_log("Client config: " . json_encode($pusher->getClientConfig()));
}
```

### Connection Testing

Test your Pusher connection:

```bash
# Test with curl
curl -X POST "https://api-{cluster}.pusherapp.com/apps/{app_id}/events" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "test-event",
    "channel": "test-channel",
    "data": "{\"message\":\"hello world\"}"
  }'
```

### Frontend Debugging

Enable Pusher logging in JavaScript:

```javascript
Pusher.logToConsole = true;

const pusher = new Pusher('your-app-key', {
    cluster: 'your-cluster',
    forceTLS: true
});
```