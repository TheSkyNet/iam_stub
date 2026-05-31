<?php

namespace IamLab\Core\SSE;

/**
 * BufferWriter captures headers and bytes into memory for testing.
 */
class BufferWriter implements OutputWriterInterface
{
    private array $headers = [];

    private string $buffer = '';

    #[\Override]
    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }

    #[\Override]
    public function write(string $bytes): void
    {
        $this->buffer .= $bytes;
    }

    #[\Override]
    public function flush(): void
    {
        // no-op
    }

    #[\Override]
    public function close(): void
    {
        // no-op
    }

    #[\Override]
    public function getBuffer(): string
    {
        return $this->buffer;
    }

    #[\Override]
    public function getHeaders(): array
    {
        return $this->headers;
    }
}
