<?php

namespace IamLab\Service;

use Exception;
use IamLab\Core\API\aAPI;
use IamLab\Model\Job;

/**
 * Jobs API Service
 * 
 * Provides REST API endpoints for job management
 */
class JobsApi extends aAPI
{
    /**
     * @var JobQueue
     */
    protected $jobQueue;

    /**
     * Initialize the service
     */
    public function initialize()
    {
        $this->jobQueue = new JobQueue();
    }

    /**
     * List jobs with pagination and filtering
     * GET /api/jobs
     */
    public function indexAction(): void
    {
        try {
            $status = $this->getParam('status', null, 'string');
            $limit = $this->getParam('limit', 50, 'int');
            $offset = $this->getParam('offset', 0, 'int');
            $type = $this->getParam('type', null, 'string');

            // Validate limit
            if ($limit > 100) {
                $limit = 100;
            }

            $conditions = [];
            $bind = [];

            if ($status) {
                $conditions[] = 'status = :status:';
                $bind['status'] = $status;
            }

            if ($type) {
                $conditions[] = 'type = :type:';
                $bind['type'] = $type;
            }

            $whereClause = !empty($conditions) ? implode(' AND ', $conditions) : '';

            $jobs = Job::find([
                'conditions' => $whereClause,
                'bind' => $bind,
                'order' => 'created_at DESC',
                'limit' => $limit,
                'offset' => $offset
            ]);

            $result = [];
            foreach ($jobs as $job) {
                $result[] = $this->formatJobData($job);
            }

            // Get total count for pagination
            $totalCount = Job::count([
                'conditions' => $whereClause,
                'bind' => $bind
            ]);

            $this->dispatch([
                'status' => 'success',
                'data' => $result,
                'pagination' => [
                    'total' => $totalCount,
                    'limit' => $limit,
                    'offset' => $offset,
                    'has_more' => ($offset + $limit) < $totalCount
                ]
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to retrieve jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show a specific job
     * GET /api/jobs/{id}
     */
    public function showAction(int $id): void
    {
        try {

            $job = $this->jobQueue->getJob($id);
            
            if (!$job) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Job not found'
                ], 404);
            }

            $this->dispatch([
                'status' => 'success',
                'data' => $this->formatJobData($job, true)
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to retrieve job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new job
     * POST /api/jobs
     */
    public function createAction(): void
    {
        try {
            $data = $this->getData();
            
            // Validate required fields
            if (!isset($data['type']) || empty($data['type'])) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Job type is required'
                ]);
            }

            $type = $data['type'];
            $payload = $data['payload'] ?? [];
            $priority = $data['priority'] ?? Job::PRIORITY_NORMAL;
            $scheduledAt = $data['scheduled_at'] ?? null;
            $maxAttempts = $data['max_attempts'] ?? 3;

            // Validate priority
            if ($priority < 1 || $priority > 15) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Priority must be between 1 and 15'
                ]);
            }

            // Validate max attempts
            if ($maxAttempts < 1 || $maxAttempts > 10) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Max attempts must be between 1 and 10'
                ]);
            }

            // Validate scheduled_at format if provided
            if ($scheduledAt && !strtotime($scheduledAt)) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Invalid scheduled_at format. Use Y-m-d H:i:s format'
                ]);
            }

            $job = $this->jobQueue->dispatch($type, $payload, $priority, $scheduledAt, $maxAttempts);
            
            if (!$job) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Failed to create job'
                ], 500);
            }

            $this->dispatch([
                'status' => 'success',
                'message' => 'Job created successfully',
                'data' => $this->formatJobData($job)
            ], 201);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to create job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a job
     * DELETE /api/jobs/{id}
     */
    public function deleteAction(int $id): void
    {
        try {

            $success = $this->jobQueue->cancelJob($id);
            
            if (!$success) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Failed to cancel job. Job may not exist or is currently processing.'
                ]);
            }

            $this->dispatch([
                'status' => 'success',
                'message' => 'Job cancelled successfully'
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to cancel job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry a failed job
     * POST /api/jobs/{id}/retry
     */
    public function retryAction($id): void
    {
        try {

            if (!$id) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Job ID is required'
                ]);
            }

            $success = $this->jobQueue->retryJob($id);
            
            if (!$success) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Failed to retry job. Job may not exist or is not in failed status.'
                ]);
            }

            $this->dispatch([
                'status' => 'success',
                'message' => 'Job queued for retry successfully'
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to retry job: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job statistics
     * GET /api/jobs/stats
     */
    public function statsAction(): void
    {
        try {
            $stats = $this->jobQueue->getStats();
            
            $this->dispatch([
                'status' => 'success',
                'data' => $stats
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to retrieve job statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clean up old completed jobs
     * POST /api/jobs/cleanup
     */
    public function cleanupAction(): void
    {
        try {
            $data = $this->getData();
            $days = $data['days'] ?? 7;

            // Validate days parameter
            if ($days < 1 || $days > 365) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Days parameter must be between 1 and 365'
                ]);
            }

            $deletedCount = $this->jobQueue->cleanup($days);
            
            $this->dispatch([
                'status' => 'success',
                'message' => "Cleaned up {$deletedCount} completed jobs older than {$days} days",
                'data' => [
                    'deleted_count' => $deletedCount,
                    'days' => $days
                ]
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to cleanup jobs: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get job types (for UI dropdowns)
     * GET /api/jobs/types
     */
    public function typesAction(): void
    {
        try {
            // Get distinct job types from database
            $result = $this->db->query("SELECT DISTINCT type FROM jobs ORDER BY type");
            $types = [];
            
            while ($row = $result->fetch()) {
                $types[] = $row['type'];
            }

            $this->dispatch([
                'status' => 'success',
                'data' => $types
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to retrieve job types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk operations on jobs
     * POST /api/jobs/bulk
     */
    public function bulkAction(): void
    {
        try {
            $data = $this->getData();
            
            if (!isset($data['action']) || !isset($data['job_ids'])) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'Action and job_ids are required'
                ]);
            }

            $action = $data['action'];
            $jobIds = $data['job_ids'];

            if (!is_array($jobIds) || empty($jobIds)) {
                $this->dispatchError([
                    'status' => 'error',
                    'message' => 'job_ids must be a non-empty array'
                ]);
            }

            $results = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($jobIds as $jobId) {
                $success = false;
                $message = '';

                switch ($action) {
                    case 'cancel':
                        $success = $this->jobQueue->cancelJob($jobId);
                        $message = $success ? 'Cancelled' : 'Failed to cancel';
                        break;
                    
                    case 'retry':
                        $success = $this->jobQueue->retryJob($jobId);
                        $message = $success ? 'Queued for retry' : 'Failed to retry';
                        break;
                    
                    default:
                        $message = 'Invalid action';
                        break;
                }

                $results[] = [
                    'job_id' => $jobId,
                    'success' => $success,
                    'message' => $message
                ];

                if ($success) {
                    $successCount++;
                } else {
                    $failureCount++;
                }
            }

            $this->dispatch([
                'status' => 'success',
                'message' => "Bulk {$action} completed: {$successCount} successful, {$failureCount} failed",
                'data' => [
                    'results' => $results,
                    'summary' => [
                        'total' => count($jobIds),
                        'successful' => $successCount,
                        'failed' => $failureCount
                    ]
                ]
            ]);

        } catch (Exception $e) {
            $this->dispatchError([
                'status' => 'error',
                'message' => 'Failed to perform bulk operation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format job data for API response
     *
     * @param Job $job
     * @param bool $includePayload
     * @return array
     */
    protected function formatJobData(Job $job, bool $includePayload = false): array
    {
        $data = [
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

        if ($includePayload) {
            $data['payload'] = $job->getPayload();
        }

        return $data;
    }
}