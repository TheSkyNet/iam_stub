<?php

namespace IamLab\Core\SSE;

/**
 * OutputWriterInterface abstracts how SSE bytes and headers are written.
 *
 * Implementations may write to php://output (runtime) or an in-memory buffer (tests).
 */
interface OutputWriterInterface
{
    /** Set/override a header on the HTTP response */
    public function setHeader(string $name, string $value): void;

    /** Write raw bytes to the output without adding newlines */
    public function write(string $bytes): void;

    /** Flush output buffers to the client (no-op for buffer writers) */
    public function flush(): void;

    /** Close underlying resources if needed (optional) */
    public function close(): void;

    /** For tests: fetch all written data if available; implementations may return empty string at runtime */
    public function getBuffer(): string;

    /** For tests: read back headers set so far */
    public function getHeaders(): array;
}
