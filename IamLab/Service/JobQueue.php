<?php

namespace IamLab\Service;

use Exception;
use IamLab\Model\Job;
use Phalcon\Di\Injectable;

/**
 * JobQueue Service
 *
 * Handles job creation, queuing, and management
 */
class JobQueue extends Injectable
{
    /**
     * Dispatch a new job
     *
     * @param string $type Job type/class name
     * @param array $payload Job data
     * @param int $priority Job priority (1-15, higher = more priority)
     * @param string|null $scheduledAt When to run the job (Y-m-d H:i:s format)
     * @param int $maxAttempts Maximum retry attempts
     * @return Job|false
     */
    public function dispatch(
        string $type,
        array $payload = [],
        int $priority = Job::PRIORITY_NORMAL,
        ?string $scheduledAt = null,
        int $maxAttempts = 3
    ): Job|false {
        try {
            $job = new Job();
            $job->setType($type)
                ->setPayload($payload)
                ->setPriority($priority)
                ->setMaxAttempts($maxAttempts)
                ->setScheduledAt($scheduledAt);

            if ($job->save()) {
                return $job;
            }

            return false;
        } catch (Exception $exception) {
            error_log("Failed to dispatch job: " . $exception->getMessage());
            return false;
        }
    }

    /**
     * Dispatch a job with high priority
     *
     * @return Job|false
     */
    public function dispatchHigh(
        string $type,
        array $payload = [],
        ?string $scheduledAt = null,
        int $maxAttempts = 3
    ): Job|false {
        return $this->dispatch($type, $payload, Job::PRIORITY_HIGH, $scheduledAt, $maxAttempts);
    }

    /**
     * Dispatch a critical priority job
     *
     * @return Job|false
     */
    public function dispatchCritical(
        string $type,
        array $payload = [],
        ?string $scheduledAt = null,
        int $maxAttempts = 3
    ): Job|false {
        return $this->dispatch($type, $payload, Job::PRIORITY_CRITICAL, $scheduledAt, $maxAttempts);
    }

    /**
     * Schedule a job to run later
     *
     * @return Job|false
     */
    public function schedule(
        string $type,
        array $payload,
        string $scheduledAt,
        int $priority = Job::PRIORITY_NORMAL,
        int $maxAttempts = 3
    ): Job|false {
        return $this->dispatch($type, $payload, $priority, $scheduledAt, $maxAttempts);
    }

    /**
     * Schedule a job to run after a delay
     *
     * @return Job|false
     */
    public function delay(
        string $type,
        array $payload,
        int $delaySeconds,
        int $priority = Job::PRIORITY_NORMAL,
        int $maxAttempts = 3
    ): Job|false {
        $scheduledAt = date('Y-m-d H:i:s', time() + $delaySeconds);
        return $this->dispatch($type, $payload, $priority, $scheduledAt, $maxAttempts);
    }

    /**
     * Get the next job to process
     */
    public function getNextJob(): ?Job
    {
        // First, try to get ready jobs
        $jobs = Job::findReadyJobs(1);
        if ($jobs->count() > 0) {
            return $jobs->getFirst();
        }

        // Then, try to get retryable jobs
        $retryJobs = Job::findRetryableJobs(1);
        if ($retryJobs->count() > 0) {
            return $retryJobs->getFirst();
        }

        return null;
    }

    /**
     * Get multiple jobs to process
     */
    public function getNextJobs(int $limit = 10): array
    {
        $jobs = [];

        // Get ready jobs
        $readyJobs = Job::findReadyJobs($limit);
        foreach ($readyJobs as $job) {
            $jobs[] = $job;
        }

        // If we need more jobs, get retryable ones
        $remaining = $limit - count($jobs);
        if ($remaining > 0) {
            $retryJobs = Job::findRetryableJobs($remaining);
            foreach ($retryJobs as $job) {
                $jobs[] = $job;
            }
        }

        return $jobs;
    }

    /**
     * Process a job
     */
    public function processJob(Job $job): bool
    {
        try {
            // Mark job as processing
            $job->markAsProcessing();
            $job->incrementAttempts();
            $job->save();

            // Get the job handler
            $handler = $this->getJobHandler($job->getType());
            if ($handler === null) {
                throw new Exception("No handler found for job type: " . $job->getType());
            }

            // Execute the job
            $result = $handler->handle($job->getPayload());

            if ($result === true || $result === null) {
                // Job completed successfully
                $job->markAsCompleted();
                $job->save();
                return true;
            }

            // Job failed
            $errorMessage = is_string($result) ? $result : "Job handler returned false";
            $this->handleJobFailure($job, $errorMessage);
            return false;
        } catch (Exception $exception) {
            $this->handleJobFailure($job, $exception->getMessage());
            return false;
        }
    }

    /**
     * Handle job failure
     */
    protected function handleJobFailure(Job $job, string $errorMessage): void
    {
        if ($job->canRetry()) {
            $job->markForRetry($errorMessage);
        } else {
            $job->markAsFailed($errorMessage);
        }

        $job->save();
    }

    /**
     * Get job handler instance
     */
    protected function getJobHandler(string $type): ?object
    {
        try {
            // Try to instantiate the job handler class
            if (class_exists($type)) {
                $handler = new $type();

                // Check if handler has a handle method
                if (method_exists($handler, 'handle')) {
                    return $handler;
                }
            }

            // Try to find handler in Jobs namespace
            $jobClass = 'IamLab\Jobs\\' . $type;
            if (class_exists($jobClass)) {
                $handler = new $jobClass();
                if (method_exists($handler, 'handle')) {
                    return $handler;
                }
            }

            return null;
        } catch (Exception $exception) {
            error_log(sprintf('Failed to create job handler for %s: ', $type) . $exception->getMessage());
            return null;
        }
    }

    /**
     * Get job statistics
     */
    public function getStats(): array
    {
        return Job::getStats();
    }

    /**
     * Get job by ID
     */
    public function getJob(int $id): ?Job
    {
        return Job::findFirst($id);
    }

    /**
     * Cancel a job
     */
    public function cancelJob(int $id): bool
    {
        $job = Job::findFirst($id);
        if (!$job instanceof Job) {
            return false;
        }

        if ($job->getStatus() === Job::STATUS_PROCESSING) {
            return false; // Cannot cancel a job that's currently processing
        }

        return $job->delete();
    }

    /**
     * Retry a failed job
     */
    public function retryJob(int $id): bool
    {
        $job = Job::findFirst($id);
        if (!$job instanceof Job) {
            return false;
        }

        if ($job->getStatus() !== Job::STATUS_FAILED) {
            return false; // Can only retry failed jobs
        }

        $job->setStatus(Job::STATUS_PENDING);
        $job->setAttempts(0);
        $job->setErrorMessage(null);
        $job->setStartedAt(null);
        $job->setCompletedAt(null);

        return $job->save();
    }

    /**
     * Clean up old completed jobs
     *
     * @return int Number of jobs deleted
     */
    public function cleanup(int $days = 7): int
    {
        return Job::cleanup($days);
    }

    /**
     * Get jobs by status
     */
    public function getJobsByStatus(string $status, int $limit = 50, int $offset = 0): array
    {
        $jobs = Job::find([
            'conditions' => 'status = :status:',
            'bind' => ['status' => $status],
            'order' => 'created_at DESC',
            'limit' => $limit,
            'offset' => $offset
        ]);

        $result = [];
        foreach ($jobs as $job) {
            $result[] = [
                'id' => $job->getId(),
                'type' => $job->getType(),
                'status' => $job->getStatus(),
                'priority' => $job->getPriority(),
                'attempts' => $job->getAttempts(),
                'max_attempts' => $job->getMaxAttempts(),
                'error_message' => $job->getErrorMessage(),
                'scheduled_at' => $job->getScheduledAt(),
                'started_at' => $job->getStartedAt(),
                'completed_at' => $job->getCompletedAt(),
                'created_at' => $job->getCreatedAt(),
                'updated_at' => $job->getUpdatedAt(),
            ];
        }

        return $result;
    }

    /**
     * Get recent jobs
     */
    public function getRecentJobs(int $limit = 50): array
    {
        $jobs = Job::find([
            'order' => 'created_at DESC',
            'limit' => $limit
        ]);

        $result = [];
        foreach ($jobs as $job) {
            $result[] = [
                'id' => $job->getId(),
                'type' => $job->getType(),
                'status' => $job->getStatus(),
                'priority' => $job->getPriority(),
                'attempts' => $job->getAttempts(),
                'max_attempts' => $job->getMaxAttempts(),
                'error_message' => $job->getErrorMessage(),
                'scheduled_at' => $job->getScheduledAt(),
                'started_at' => $job->getStartedAt(),
                'completed_at' => $job->getCompletedAt(),
                'created_at' => $job->getCreatedAt(),
                'updated_at' => $job->getUpdatedAt(),
            ];
        }

        return $result;
    }
}
