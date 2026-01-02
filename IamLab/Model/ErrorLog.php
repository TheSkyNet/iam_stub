<?php

namespace IamLab\Model;

// Provide a conditional base so tests can run without Phalcon extension
if (class_exists('Phalcon\\Mvc\\Model')) {
    class ErrorLogBase extends \Phalcon\Mvc\Model {}
} else {
    class ErrorLogBase {}
}

class ErrorLog extends ErrorLogBase
{
    protected ?int $id = null;
    protected string $level = 'error';
    protected string $message = '';
    protected ?string $context = null; // JSON string
    protected ?string $url = null;
    protected ?string $user_agent = null;
    protected ?string $ip = null;
    protected ?int $user_id = null;
    protected ?string $created_at = null;

    public function initialize()
    {
        // Only call setSource when running under Phalcon's Model
        if (method_exists($this, 'setSource')) {
            $this->setSource('error_logs');
        }
    }

    public function beforeValidationOnCreate()
    {
        if (!$this->created_at) {
            $this->created_at = date('Y-m-d H:i:s');
        }
    }

    public function setLevel(string $level): static { $this->level = $level; return $this; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }
    public function setContext(?array $context): static { $this->context = $context ? json_encode($context, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) : null; return $this; }
    public function setContextJson(?string $json): static { $this->context = $json; return $this; }
    public function setUrl(?string $url): static { $this->url = $url; return $this; }
    public function setUserAgent(?string $ua): static { $this->user_agent = $ua; return $this; }
    public function setIp(?string $ip): static { $this->ip = $ip; return $this; }
    public function setUserId(?int $userId): static { $this->user_id = $userId; return $this; }

    public function getId(): ?int { return $this->id; }
    public function getLevel(): string { return $this->level; }
    public function getMessage(): string { return $this->message; }
    public function getContext(): ?array { return $this->context ? json_decode($this->context, true) : null; }
    public function getContextJson(): ?string { return $this->context; }
    public function getUrl(): ?string { return $this->url; }
    public function getUserAgent(): ?string { return $this->user_agent; }
    public function getIp(): ?string { return $this->ip; }
    public function getUserId(): ?int { return $this->user_id; }
    public function getCreatedAt(): ?string { return $this->created_at; }
}
