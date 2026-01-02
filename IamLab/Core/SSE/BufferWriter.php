<?php

namespace IamLab\Core\SSE;

/**
 * BufferWriter captures headers and bytes into memory for testing.
 */
class BufferWriter implements OutputWriterInterface
{
    private array $headers = [];
    private string $buffer = '';

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    public function write(string $bytes): void
    {
        $this->buffer .= $bytes;
    }

    public function flush(): void
    {
        // no-op
    }

    public function close(): void
    {
        // no-op
    }

    public function getBuffer(): string
    {
        return $this->buffer;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
