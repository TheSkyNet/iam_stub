<?php

namespace Tests\Unit\Core\SSE;

use IamLab\Core\SSE\BufferWriter;
use IamLab\Core\SSE\SseEmitter;
use PHPUnit\Framework\TestCase;

class SseEmitterTest extends TestCase
{
    public function testStartSetsHeadersAndOptionalRetry(): void
    {
        $writer = new BufferWriter();
        $emitter = new SseEmitter($writer);
        $emitter->start(2500);

        $headers = $writer->getHeaders();
        $this->assertSame('text/event-stream; charset=utf-8', $headers['Content-Type'] ?? null);
        $this->assertSame('no-cache', $headers['Cache-Control'] ?? null);
        $this->assertSame('keep-alive', $headers['Connection'] ?? null);
        $this->assertSame('no', $headers['X-Accel-Buffering'] ?? null);

        $buf = $writer->getBuffer();
        $this->assertStringContainsString("retry: 2500\n\n", $buf);
    }

    public function testSendFormatsEventIdAndJsonData(): void
    {
        $writer = new BufferWriter();
        $emitter = new SseEmitter($writer);
        $emitter->start();

        $emitter->send(['a' => 1, 'b' => 'x'], 'test', '42');

        $buf = $writer->getBuffer();
        $this->assertStringContainsString("id: 42\n", $buf);
        $this->assertStringContainsString("event: test\n", $buf);
        $this->assertStringContainsString("data: {\"a\":1,\"b\":\"x\"}\n\n", $buf);
    }

    public function testCommentAndHeartbeat(): void
    {
        $writer = new BufferWriter();
        $emitter = new SseEmitter($writer);
        $emitter->comment('keepalive');
        $emitter->heartbeat();

        $buf = $writer->getBuffer();
        $this->assertStringContainsString(": keepalive\n\n", $buf);
        $this->assertStringContainsString(": heartbeat ", $buf);
    }
}
