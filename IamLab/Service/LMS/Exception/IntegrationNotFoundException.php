<?php

namespace IamLab\Service\LMS\Exception;

/**
 * Exception thrown when a requested integration is not found or supported
 */
class IntegrationNotFoundException extends LMSException
{
    public function __construct(string $message = "", int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}