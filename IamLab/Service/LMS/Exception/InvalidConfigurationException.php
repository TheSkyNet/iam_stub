<?php

namespace IamLab\Service\LMS\Exception;

use Throwable;

/**
 * Exception thrown when integration configuration is invalid
 */
class InvalidConfigurationException extends LMSException
{
    private array $validationErrors = [];

    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null, array $validationErrors = [])
    {
        parent::__construct($message, $code, $previous);
        $this->validationErrors = $validationErrors;
    }

    /**
     * Get validation errors
     */
    public function getValidationErrors(): array
    {
        return $this->validationErrors;
    }

    /**
     * Set validation errors
     */
    public function setValidationErrors(array $errors): self
    {
        $this->validationErrors = $errors;
        return $this;
    }

    /**
     * Add validation error
     */
    public function addValidationError(string $error): self
    {
        $this->validationErrors[] = $error;
        return $this;
    }

    /**
     * Get formatted error information including validation errors
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        $data['validation_errors'] = $this->validationErrors;
        return $data;
    }
}