<?php

namespace IamLab\Core\Command;

use Exception;
use IamLab\Model\Job;
use IamLab\Service\JobQueue;

/**
 * Worker Command
 * 
 * Processes jobs from the queue
 */
class WorkerCommand extends BaseCommand
{
    /**
     * @var JobQueue
     */
    protected $jobQueue;

    /**
     * @var bool
     */
    protected $shouldStop = false;

    /**
     * Command signature
     */
    public function getSignature(): string
    {
        return 'worker:run [--jobs=] [--timeout=] [--sleep=] [--max-memory=] [--once]';
    }

    /**
     * Command description
     */
    public function getDescription(): string
    {
        return 'Run the job queue worker to process pending jobs';
    }

    /**
     * Command help
     */
    public function getHelp(): string
    {
        return 'This command starts a worker process that continuously processes jobs from the queue.

Options:
  --jobs=N         Maximum number of jobs to process (default: unlimited)
  --timeout=N      Maximum execution time in seconds (default: 3600)
  --sleep=N        Sleep time in seconds when no jobs are available (default: 3)
  --max-memory=N   Maximum memory usage in MB before restarting (default: 128)
  --once           Process only one job and exit

Examples:
  worker:run                    # Run worker continuously
  worker:run --once             # Process one job and exit
  worker:run --jobs=10          # Process maximum 10 jobs
  worker:run --timeout=1800     # Run for maximum 30 minutes
  worker:run --sleep=5          # Sleep 5 seconds when no jobs available';
    }

    /**
     * Handle the command
     */
    public function handle(): int
    {
        $this->jobQueue = new JobQueue();

        // Get options
        $maxJobs = $this->option('jobs', null);
        $timeout = $this->option('timeout', 3600);
        $sleepTime = $this->option('sleep', 3);
        $maxMemory = $this->option('max-memory', 128);
        $once = $this->hasOption('once');

        // Validate options
        if ($maxJobs !== null && (!is_numeric($maxJobs) || $maxJobs < 1)) {
            $this->error('Invalid jobs option. Must be a positive integer.');
            return 1;
        }

        if (!is_numeric($timeout) || $timeout < 1) {
            $this->error('Invalid timeout option. Must be a positive integer.');
            return 1;
        }

        if (!is_numeric($sleepTime) || $sleepTime < 1) {
            $this->error('Invalid sleep option. Must be a positive integer.');
            return 1;
        }

        if (!is_numeric($maxMemory) || $maxMemory < 1) {
            $this->error('Invalid max-memory option. Must be a positive integer.');
            return 1;
        }

        // Convert to appropriate types
        $maxJobs = $maxJobs ? (int)$maxJobs : null;
        $timeout = (int)$timeout;
        $sleepTime = (int)$sleepTime;
        $maxMemory = (int)$maxMemory * 1024 * 1024; // Convert MB to bytes

        $this->info('Starting job queue worker...');
        $this->info("Configuration:");
        $this->info("  Max jobs: " . ($maxJobs ? $maxJobs : 'unlimited'));
        $this->info("  Timeout: {$timeout} seconds");
        $this->info("  Sleep time: {$sleepTime} seconds");
        $this->info("  Max memory: " . ($maxMemory / 1024 / 1024) . " MB");
        $this->info("  Run once: " . ($once ? 'yes' : 'no'));
        $this->line('');

        // Set up signal handlers for graceful shutdown
        $this->setupSignalHandlers();

        $startTime = time();
        $processedJobs = 0;

        try {
            while (!$this->shouldStop) {
                // Check timeout
                if (time() - $startTime >= $timeout) {
                    $this->info('Worker timeout reached. Shutting down gracefully...');
                    break;
                }

                // Check memory usage
                if (memory_get_usage() > $maxMemory) {
                    $this->warn('Memory limit reached. Shutting down gracefully...');
                    break;
                }

                // Check max jobs limit
                if ($maxJobs && $processedJobs >= $maxJobs) {
                    $this->info("Maximum jobs ({$maxJobs}) processed. Shutting down...");
                    break;
                }

                // Get next job
                $job = $this->jobQueue->getNextJob();

                if (!$job) {
                    if ($once) {
                        $this->info('No jobs available. Exiting (--once mode).');
                        break;
                    }

                    $this->verbose("No jobs available. Sleeping for {$sleepTime} seconds...");
                    sleep($sleepTime);
                    continue;
                }

                // Process the job
                $this->processJob($job);
                $processedJobs++;

                if ($once) {
                    $this->info('Job processed. Exiting (--once mode).');
                    break;
                }

                // Small delay to prevent overwhelming the system
                usleep(100000); // 0.1 seconds
            }

        } catch (Exception $e) {
            $this->error('Worker encountered an error: ' . $e->getMessage());
            return 1;
        }

        $duration = time() - $startTime;
        $this->success("Worker finished. Processed {$processedJobs} jobs in {$duration} seconds.");
        
        return 0;
    }

    /**
     * Process a single job
     *
     * @param Job $job
     */
    protected function processJob(Job $job): void
    {
        $this->info("Processing job #{$job->getId()} ({$job->getType()})...");
        
        $startTime = microtime(true);
        
        try {
            $success = $this->jobQueue->processJob($job);
            
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            
            if ($success) {
                $this->success("✓ Job #{$job->getId()} completed successfully in {$duration}ms");
            } else {
                $this->warn("⚠ Job #{$job->getId()} failed in {$duration}ms");
                if ($job->getErrorMessage()) {
                    $this->error("  Error: " . $job->getErrorMessage());
                }
            }
            
        } catch (Exception $e) {
            $duration = round((microtime(true) - $startTime) * 1000, 2);
            $this->error("✗ Job #{$job->getId()} threw exception in {$duration}ms: " . $e->getMessage());
        }
    }

    /**
     * Set up signal handlers for graceful shutdown
     */
    protected function setupSignalHandlers(): void
    {
        if (function_exists('pcntl_signal')) {
            // Handle SIGTERM and SIGINT for graceful shutdown
            pcntl_signal(SIGTERM, [$this, 'handleShutdownSignal']);
            pcntl_signal(SIGINT, [$this, 'handleShutdownSignal']);
            
            $this->verbose('Signal handlers registered for graceful shutdown.');
        } else {
            $this->verbose('PCNTL extension not available. Signal handling disabled.');
        }
    }

    /**
     * Handle shutdown signals
     *
     * @param int $signal
     */
    public function handleShutdownSignal(int $signal): void
    {
        $signalName = $signal === SIGTERM ? 'SIGTERM' : 'SIGINT';
        $this->warn("Received {$signalName}. Shutting down gracefully...");
        $this->shouldStop = true;
    }

    /**
     * Get worker status information
     */
    public function getWorkerStatus(): array
    {
        $stats = $this->jobQueue->getStats();
        
        return [
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'job_stats' => $stats,
            'uptime' => time() - $_SERVER['REQUEST_TIME'],
        ];
    }

    /**
     * Display worker statistics
     */
    protected function displayStats(): void
    {
        $stats = $this->jobQueue->getStats();
        
        $this->line('');
        $this->info('Job Queue Statistics:');
        $this->line("  Pending: {$stats[Job::STATUS_PENDING]}");
        $this->line("  Processing: {$stats[Job::STATUS_PROCESSING]}");
        $this->line("  Completed: {$stats[Job::STATUS_COMPLETED]}");
        $this->line("  Failed: {$stats[Job::STATUS_FAILED]}");
        $this->line("  Retrying: {$stats[Job::STATUS_RETRYING]}");
        
        $memoryUsage = round(memory_get_usage(true) / 1024 / 1024, 2);
        $memoryPeak = round(memory_get_peak_usage(true) / 1024 / 1024, 2);
        
        $this->line('');
        $this->info('Memory Usage:');
        $this->line("  Current: {$memoryUsage} MB");
        $this->line("  Peak: {$memoryPeak} MB");
        $this->line('');
    }
}