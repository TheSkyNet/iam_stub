<?php

namespace IamLab\Core\SSE;

/**
 * PhpOutputWriter sends headers with PHP's header() and writes bytes with echo.
 */
class PhpOutputWriter implements OutputWriterInterface
{
    private array $headers = [];

    public function setHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
        // Only set header if not already sent; suppress errors if headers are already sent
        if (!headers_sent()) {
            @header($name . ': ' . $value, true);
        }
    }

    public function write(string $bytes): void
    {
        echo $bytes;
    }

    public function flush(): void
    {
        if (function_exists('fastcgi_finish_request')) {
            // We do not want to terminate request, so just flush buffers
        }
        @ob_flush();
        @flush();
    }

    public function close(): void
    {
        // Nothing special to close for php output
    }

    public function getBuffer(): string
    {
        return '';
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }
}
