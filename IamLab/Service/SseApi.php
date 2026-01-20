<?php

namespace IamLab\Service;

use IamLab\Core\API\aAPI;
use IamLab\Core\SSE\PhpOutputWriter;
use IamLab\Core\SSE\OutputWriterInterface;
use IamLab\Core\SSE\SseEmitter;
use JetBrains\PhpStorm\NoReturn;

/**
 * SseApi provides Server-Sent Events endpoints.
 */
class SseApi extends aAPI
{
    /**
     * GET /api/sse/clock
     * Streams the server time every second for a limited number of ticks (default 10).
     * Optional query params: count (int), interval (ms), retry (ms)
     */
    public function clockAction(): void
    {
        // Read from query string since this is a GET endpoint
        $count = (int)($this->getQuery('count', 10));
        $intervalMs = (int)($this->getQuery('interval', 1000));
        $retry = (int)($this->getQuery('retry', 2000));

        $emitter = $this->createEmitter($retry);

        for ($i = 0; $i < $count; $i++) {
            $payload = $this->buildTickPayload($i);
            $emitter->send($payload, 'tick', (string)$i);
            $this->sleepMs($intervalMs);
        }

        $emitter->end();
        $this->terminate(); // Ensure Micro app stops here
    }

    /**
     * GET /api/sse/echo?message=Hello
     * Sends a single event and closes the stream.
     */
    public function echoAction(): void
    {
        $message = (string)$this->getQuery('message', 'Hello from SSE!');
        $retry = (int)$this->getQuery('retry', 2000);
        $emitter = $this->createEmitter($retry);
        $emitter->send([
            'message' => $message,
            'time' => gmdate('c'),
        ], 'echo');
        $emitter->end();
        $this->terminate();
    }

    /**
     * GET /api/sse/test
     * Two quick events for basic smoke testing.
     */
    public function testAction(): void
    {
        $emitter = $this->createEmitter(1000);
        $emitter->send(['message' => 'test-1', 'time' => gmdate('c')], 'test');
        $emitter->send(['message' => 'test-2', 'time' => gmdate('c')], 'test');
        $emitter->end();
        $this->terminate();
    }


    protected function createEmitter(?int $retryMs = null): SseEmitter
    {
        $writer = $this->createWriter();
        $emitter = new SseEmitter($writer);
        $emitter->start($retryMs);
        return $emitter;
    }

    protected function createWriter(): OutputWriterInterface
    {
        return new PhpOutputWriter();
    }

    protected function buildTickPayload(int $i): array
    {
        return [
            'time' => gmdate('c'),
            'index' => $i,
        ];
    }

    protected function sleepMs(int $ms): void
    {
        if ($ms <= 0) { return; }
        usleep($ms * 1000);
    }

    /**
     * Allow tests to override termination behavior.
     */
    #[NoReturn]
    protected function terminate(): void
    {
        exit;
    }

    /**
     * Small helper to retrieve GET parameters with default.
     */
    protected function getQuery(string $name, mixed $default = null): mixed
    {
        return $this->request->getQuery($name, null, $default);
    }
}
