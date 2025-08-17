<?php

namespace IamLab\Model;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\ResultsetInterface;

/**
 * Job Model
 * 
 * Represents a job in the queue system
 */
class Job extends Model
{
    /**
     * Job statuses
     */
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_RETRYING = 'retrying';

    /**
     * Job priorities
     */
    const PRIORITY_LOW = 1;
    const PRIORITY_NORMAL = 5;
    const PRIORITY_HIGH = 10;
    const PRIORITY_CRITICAL = 15;

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $type;

    /**
     * @var string JSON encoded payload
     */
    public $payload;

    /**
     * @var string
     */
    public $status;

    /**
     * @var int
     */
    public $priority;

    /**
     * @var int
     */
    public $attempts;

    /**
     * @var int
     */
    public $max_attempts;

    /**
     * @var string
     */
    public $error_message;

    /**
     * @var string
     */
    public $scheduled_at;

    /**
     * @var string
     */
    public $started_at;

    /**
     * @var string
     */
    public $completed_at;

    /**
     * @var string
     */
    public $created_at;

    /**
     * @var string
     */
    public $updated_at;

    /**
     * Get job ID
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set job type
     */
    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get job type
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * Set job payload
     */
    public function setPayload(array $payload): static
    {
        $this->payload = json_encode($payload);
        return $this;
    }

    /**
     * Get job payload as array
     */
    public function getPayload(): array
    {
        return json_decode($this->payload ?? '{}', true);
    }

    /**
     * Set job status
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get job status
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * Set job priority
     */
    public function setPriority(int $priority): static
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Get job priority
     */
    public function getPriority(): int
    {
        return $this->priority ?? self::PRIORITY_NORMAL;
    }

    /**
     * Set attempts count
     */
    public function setAttempts(int $attempts): static
    {
        $this->attempts = $attempts;
        return $this;
    }

    /**
     * Get attempts count
     */
    public function getAttempts(): int
    {
        return $this->attempts ?? 0;
    }

    /**
     * Set max attempts
     */
    public function setMaxAttempts(int $max_attempts): static
    {
        $this->max_attempts = $max_attempts;
        return $this;
    }

    /**
     * Get max attempts
     */
    public function getMaxAttempts(): int
    {
        return $this->max_attempts ?? 3;
    }

    /**
     * Set error message
     */
    public function setErrorMessage(?string $error_message): static
    {
        $this->error_message = $error_message;
        return $this;
    }

    /**
     * Get error message
     */
    public function getErrorMessage(): ?string
    {
        return $this->error_message;
    }

    /**
     * Set scheduled at timestamp
     */
    public function setScheduledAt(?string $scheduled_at): static
    {
        $this->scheduled_at = $scheduled_at;
        return $this;
    }

    /**
     * Get scheduled at timestamp
     */
    public function getScheduledAt(): ?string
    {
        return $this->scheduled_at;
    }

    /**
     * Set started at timestamp
     */
    public function setStartedAt(?string $started_at): static
    {
        $this->started_at = $started_at;
        return $this;
    }

    /**
     * Get started at timestamp
     */
    public function getStartedAt(): ?string
    {
        return $this->started_at;
    }

    /**
     * Set completed at timestamp
     */
    public function setCompletedAt(?string $completed_at): static
    {
        $this->completed_at = $completed_at;
        return $this;
    }

    /**
     * Get completed at timestamp
     */
    public function getCompletedAt(): ?string
    {
        return $this->completed_at;
    }

    /**
     * Set created at timestamp
     */
    public function setCreatedAt(string $created_at): static
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * Get created at timestamp
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * Set updated at timestamp
     */
    public function setUpdatedAt(string $updated_at): static
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * Get updated at timestamp
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * Check if job can be retried
     */
    public function canRetry(): bool
    {
        return $this->getAttempts() < $this->getMaxAttempts();
    }

    /**
     * Check if job is ready to run
     */
    public function isReady(): bool
    {
        if ($this->scheduled_at === null) {
            return true;
        }
        
        return strtotime($this->scheduled_at) <= time();
    }

    /**
     * Increment attempts counter
     */
    public function incrementAttempts(): static
    {
        $this->attempts = $this->getAttempts() + 1;
        return $this;
    }

    /**
     * Mark job as processing
     */
    public function markAsProcessing(): static
    {
        $this->setStatus(self::STATUS_PROCESSING);
        $this->setStartedAt(date('Y-m-d H:i:s'));
        return $this;
    }

    /**
     * Mark job as completed
     */
    public function markAsCompleted(): static
    {
        $this->setStatus(self::STATUS_COMPLETED);
        $this->setCompletedAt(date('Y-m-d H:i:s'));
        return $this;
    }

    /**
     * Mark job as failed
     */
    public function markAsFailed(string $error = null): static
    {
        $this->setStatus(self::STATUS_FAILED);
        if ($error) {
            $this->setErrorMessage($error);
        }
        return $this;
    }

    /**
     * Mark job for retry
     */
    public function markForRetry(string $error = null): static
    {
        $this->setStatus(self::STATUS_RETRYING);
        if ($error) {
            $this->setErrorMessage($error);
        }
        return $this;
    }

    /**
     * Initialize method for model
     */
    public function initialize()
    {
        $this->setSource('jobs');
        
        // Set default values
        $this->keepSnapshots(true);
    }

    /**
     * Before create hook
     */
    public function beforeCreate()
    {
        $this->created_at = date('Y-m-d H:i:s');
        $this->updated_at = date('Y-m-d H:i:s');
        
        // Set defaults
        if (!$this->status) {
            $this->status = self::STATUS_PENDING;
        }
        if (!$this->priority) {
            $this->priority = self::PRIORITY_NORMAL;
        }
        if (!$this->attempts) {
            $this->attempts = 0;
        }
        if (!$this->max_attempts) {
            $this->max_attempts = 3;
        }
    }

    /**
     * Before update hook
     */
    public function beforeUpdate()
    {
        $this->updated_at = date('Y-m-d H:i:s');
    }

    /**
     * Find jobs by status
     */
    public static function findByStatus(string $status): ResultsetInterface
    {
        return static::find([
            'conditions' => 'status = :status:',
            'bind' => ['status' => $status],
            'order' => 'priority DESC, created_at ASC'
        ]);
    }

    /**
     * Find pending jobs ready to run
     */
    public static function findReadyJobs(int $limit = 10): ResultsetInterface
    {
        return static::find([
            'conditions' => 'status = :status: AND (scheduled_at IS NULL OR scheduled_at <= :now:)',
            'bind' => [
                'status' => self::STATUS_PENDING,
                'now' => date('Y-m-d H:i:s')
            ],
            'order' => 'priority DESC, created_at ASC',
            'limit' => $limit
        ]);
    }

    /**
     * Find failed jobs that can be retried
     */
    public static function findRetryableJobs(int $limit = 10): ResultsetInterface
    {
        return static::find([
            'conditions' => 'status = :status: AND attempts < max_attempts',
            'bind' => ['status' => self::STATUS_RETRYING],
            'order' => 'priority DESC, created_at ASC',
            'limit' => $limit
        ]);
    }

    /**
     * Get job statistics
     */
    public static function getStats(): array
    {
        $stats = [];
        
        $statuses = [
            self::STATUS_PENDING,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETED,
            self::STATUS_FAILED,
            self::STATUS_RETRYING
        ];
        
        foreach ($statuses as $status) {
            $count = static::count([
                'conditions' => 'status = :status:',
                'bind' => ['status' => $status]
            ]);
            $stats[$status] = $count;
        }
        
        return $stats;
    }

    /**
     * Clean up old completed jobs
     */
    public static function cleanup(int $days = 7): int
    {
        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        $jobs = static::find([
            'conditions' => 'status = :status: AND completed_at < :cutoff:',
            'bind' => [
                'status' => self::STATUS_COMPLETED,
                'cutoff' => $cutoff
            ]
        ]);
        
        $deleted = 0;
        foreach ($jobs as $job) {
            if ($job->delete()) {
                $deleted++;
            }
        }
        
        return $deleted;
    }

    /**
     * Find method override
     */
    public static function find($parameters = null): ResultsetInterface
    {
        return parent::find($parameters);
    }

    /**
     * FindFirst method override
     */
    public static function findFirst($parameters = null): ?Job
    {
        return parent::findFirst($parameters);
    }
}