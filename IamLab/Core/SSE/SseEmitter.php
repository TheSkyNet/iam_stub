<?php

namespace IamLab\Core\SSE;

/**
 * SseEmitter: A small SOLID component to send Server-Sent Events.
 * - Single Responsibility: formats SSE frames and manages streaming headers/flushes
 * - Open/Closed: can extend or replace OutputWriterInterface without changing emitter
 * - Liskov: writer implementations can be swapped
 * - Interface Segregation: writer keeps IO concerns minimal
 * - Dependency Inversion: depends on OutputWriterInterface abstraction
 */
class SseEmitter
{
    private OutputWriterInterface $writer;
    private bool $started = false;
    private ?int $retryMs = null;

    public function __construct(OutputWriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * Prepare headers and start the SSE stream. Safe to call multiple times.
     */
    public function start(?int $retryMs = null): void
    {
        if ($this->started) {
            return;
        }
        $this->started = true;

        // Standard SSE headers
        $this->writer->setHeader('Content-Type', 'text/event-stream; charset=utf-8');
        $this->writer->setHeader('Cache-Control', 'no-cache');
        $this->writer->setHeader('Connection', 'keep-alive');
        $this->writer->setHeader('X-Accel-Buffering', 'no'); // Disable Nginx buffering if applicable

        // Optional retry directive (reconnection time in ms)
        if ($retryMs !== null) {
            $this->retryMs = $retryMs;
            $this->writer->write('retry: ' . (int)$retryMs . "\n\n");
        }

        $this->writer->flush();
    }

    /**
     * Send a single SSE event. Data may be string|scalar|array|object.
     */
    public function send(mixed $data, ?string $event = null, ?string $id = null): void
    {
        if (!$this->started) {
            $this->start($this->retryMs);
        }

        if ($id !== null) {
            $this->writer->write('id: ' . $this->sanitize($id) . "\n");
        }
        if ($event !== null) {
            $this->writer->write('event: ' . $this->sanitize($event) . "\n");
        }

        $payload = $this->normalizeData($data);
        foreach (preg_split("/\r?\n/", $payload) as $line) {
            $this->writer->write('data: ' . $line . "\n");
        }

        $this->writer->write("\n");
        $this->writer->flush();
    }

    /**
     * Send a comment line that most browsers ignore but keeps the connection alive.
     */
    public function comment(string $text = 'heartbeat'): void
    {
        if (!$this->started) {
            $this->start($this->retryMs);
        }
        foreach (preg_split("/\r?\n/", $text) as $line) {
            $this->writer->write(': ' . $line . "\n");
        }
        $this->writer->write("\n");
        $this->writer->flush();
    }

    /**
     * Convenience heartbeat method.
     */
    public function heartbeat(): void
    {
        $this->comment('heartbeat ' . gmdate('c'));
    }

    /**
     * Finish the stream by closing writer (clients keep the connection until server closes).
     */
    public function end(): void
    {
        $this->writer->close();
    }

    public function getWriter(): OutputWriterInterface
    {
        return $this->writer;
    }

    private function normalizeData(mixed $data): string
    {
        if (is_string($data)) {
            return $data;
        }
        if (is_scalar($data)) {
            return (string)$data;
        }
        // array/object => JSON
        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function sanitize(string $s): string
    {
        // Remove CR/LF to keep single-line fields
        return str_replace(["\r", "\n"], ' ', $s);
    }
}
