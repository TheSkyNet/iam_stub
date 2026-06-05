<?php

namespace IamLab\Core\WebSockets;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class WebSocketHandler implements MessageComponentInterface
{
    protected \SplObjectStorage $clients;

    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
        
        $conn->send(json_encode([
            'type' => 'welcome',
            'message' => 'Connected to IamLab WebSocket Server',
            'id' => $conn->resourceId
        ]));
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        echo sprintf("Connection %d sending message \"%s\"\n", $from->resourceId, $msg);

        $data = json_decode($msg, true);
        
        // Broadcast to all clients
        foreach ($this->clients as $client) {
            $client->send(json_encode([
                'type' => 'broadcast',
                'from' => $from->resourceId,
                'message' => $data['message'] ?? $msg,
                'time' => date('Y-m-d H:i:s')
            ]));
        }
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e): void
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}
