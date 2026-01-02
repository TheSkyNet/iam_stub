<?php

namespace Tests\Unit\Service;

use IamLab\Core\SSE\BufferWriter;
use IamLab\Core\SSE\OutputWriterInterface;
use IamLab\Service\SseApi;
use PHPUnit\Framework\TestCase;

class SseApiTest extends TestCase
{
    public function testEchoActionOutputsSingleEvent(): void
    {
        $api = new class extends SseApi {
            public BufferWriter $writer;
            protected array $queries = [
                'message' => 'Hi there',
                'retry' => 1234,
            ];
            protected function createWriter(): OutputWriterInterface { return $this->writer = new BufferWriter(); }
            protected function terminate(): void { /* no-op for tests */ }
            protected function getQuery(string $name, mixed $default = null): mixed { return $this->queries[$name] ?? $default; }
        };

        $api->echoAction();

        $headers = $api->writer->getHeaders();
        $this->assertSame('text/event-stream; charset=utf-8', $headers['Content-Type'] ?? null);
        $this->assertSame('no-cache', $headers['Cache-Control'] ?? null);
        $this->assertSame('keep-alive', $headers['Connection'] ?? null);

        $buf = $api->writer->getBuffer();
        $this->assertStringContainsString("retry: 1234\n\n", $buf);
        $this->assertStringContainsString("event: echo\n", $buf);
        $this->assertStringContainsString('"message":"Hi there"', $buf);
    }

    public function testClockActionEmitsRequestedCount(): void
    {
        $api = new class extends SseApi {
            public BufferWriter $writer;
            protected array $queries = [
                'count' => 3,
                'interval' => 0,
                'retry' => 0,
            ];
            protected function createWriter(): OutputWriterInterface { return $this->writer = new BufferWriter(); }
            protected function sleepMs(int $ms): void { /* skip delay in tests */ }
            protected function terminate(): void { /* no-op for tests */ }
            protected function getQuery(string $name, mixed $default = null): mixed { return $this->queries[$name] ?? $default; }
        };

        $api->clockAction();

        $buf = $api->writer->getBuffer();
        // Count occurrences of 'event: tick' which should equal 3
        $this->assertSame(3, substr_count($buf, "event: tick\n"));
        // Ensure ids 0,1,2 appear
        $this->assertStringContainsString("id: 0\n", $buf);
        $this->assertStringContainsString("id: 1\n", $buf);
        $this->assertStringContainsString("id: 2\n", $buf);
    }
}
