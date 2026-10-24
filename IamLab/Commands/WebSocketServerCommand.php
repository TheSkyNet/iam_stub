<?php

namespace IamLab\Commands;

use React\EventLoop\Factory;
use React\Socket\Server;
use IamLab\Core\Command\BaseCommand;
use IamLab\Core\WebSockets\WebSocketHandler;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class WebSocketServerCommand extends BaseCommand
{
    public function getSignature(): string
    {
        return 'websocket:serve';
    }

    public function getDescription(): string
    {
        return 'Start the native WebSocket server';
    }

    protected function handle(): int
    {
        $port = (int)$this->option('port', 8081);
        $host = $this->option('host', '0.0.0.0');

        $this->info("Starting WebSocket server on {$host}:{$port}...");

        $loop = Factory::create();
        $socket = new Server($loop);
        $socket->listen($port, $host);

        $server = new IoServer(
            new HttpServer(
                new WsServer(
                    new WebSocketHandler()
                )
            ),
            $socket,
            $loop
        );

        $server->run();

        return 0;
    }
}
