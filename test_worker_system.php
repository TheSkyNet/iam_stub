<?php

/**
 * Worker System Test Script
 * 
 * This script demonstrates the worker system functionality
 * Run this script to test job creation and processing
 */

require_once 'vendor/autoload.php';

use IamLab\Service\JobQueue;
use IamLab\Model\Job;

echo "=== Worker System Test Script ===\n\n";

// Initialize the job queue service
$jobQueue = new JobQueue();

echo "1. Creating test jobs...\n";

// Test 1: Create an email job
$emailJob = $jobQueue->dispatch('SendEmailJob', [
    'to' => 'test@example.com',
    'subject' => 'Test Email from Worker System',
    'message' => 'This is a test email sent via the job queue system.',
    'from' => 'worker@example.com'
], Job::PRIORITY_HIGH);

if ($emailJob) {
    echo "   ✓ Email job created (ID: {$emailJob->getId()})\n";
} else {
    echo "   ✗ Failed to create email job\n";
}

// Test 2: Create a data processing job
$dataJob = $jobQueue->dispatch('ProcessDataJob', [
    'data_type' => 'json',
    'data' => [
        ['name' => 'John', 'age' => 30],
        ['name' => 'Jane', 'age' => 25],
        ['name' => 'Bob', 'age' => 35]
    ],
    'options' => []
], Job::PRIORITY_NORMAL);

if ($dataJob) {
    echo "   ✓ Data processing job created (ID: {$dataJob->getId()})\n";
} else {
    echo "   ✗ Failed to create data processing job\n";
}

// Test 3: Create a cleanup job
$cleanupJob = $jobQueue->dispatch('CleanupJob', [
    'cleanup_type' => 'temp_files',
    'options' => [
        'temp_dir' => '/tmp',
        'max_age_hours' => 24
    ]
], Job::PRIORITY_LOW);

if ($cleanupJob) {
    echo "   ✓ Cleanup job created (ID: {$cleanupJob->getId()})\n";
} else {
    echo "   ✗ Failed to create cleanup job\n";
}

// Test 4: Create a scheduled job (runs in 5 minutes)
$scheduledJob = $jobQueue->schedule('SendEmailJob', [
    'to' => 'scheduled@example.com',
    'subject' => 'Scheduled Email',
    'message' => 'This email was scheduled to be sent later.',
], date('Y-m-d H:i:s', time() + 300)); // 5 minutes from now

if ($scheduledJob) {
    echo "   ✓ Scheduled job created (ID: {$scheduledJob->getId()}, scheduled for: {$scheduledJob->getScheduledAt()})\n";
} else {
    echo "   ✗ Failed to create scheduled job\n";
}

echo "\n2. Job queue statistics:\n";
$stats = $jobQueue->getStats();
foreach ($stats as $status => $count) {
    echo "   {$status}: {$count}\n";
}

echo "\n3. Processing jobs (simulated)...\n";

// Get and process a few jobs
$jobs = $jobQueue->getNextJobs(3);
foreach ($jobs as $job) {
    echo "   Processing job #{$job->getId()} ({$job->getType()})...\n";
    
    $startTime = microtime(true);
    $success = $jobQueue->processJob($job);
    $duration = round((microtime(true) - $startTime) * 1000, 2);
    
    if ($success) {
        echo "   ✓ Job #{$job->getId()} completed successfully in {$duration}ms\n";
    } else {
        echo "   ✗ Job #{$job->getId()} failed in {$duration}ms\n";
        if ($job->getErrorMessage()) {
            echo "     Error: {$job->getErrorMessage()}\n";
        }
    }
}

echo "\n4. Updated job queue statistics:\n";
$stats = $jobQueue->getStats();
foreach ($stats as $status => $count) {
    echo "   {$status}: {$count}\n";
}

echo "\n5. API Usage Examples:\n";
echo "   To use the worker system via API:\n\n";

echo "   Create a job:\n";
echo "   POST /api/jobs\n";
echo "   {\n";
echo "     \"type\": \"SendEmailJob\",\n";
echo "     \"payload\": {\n";
echo "       \"to\": \"user@example.com\",\n";
echo "       \"subject\": \"Hello\",\n";
echo "       \"message\": \"Hello from the job queue!\"\n";
echo "     },\n";
echo "     \"priority\": 5\n";
echo "   }\n\n";

echo "   List jobs:\n";
echo "   GET /api/jobs?status=pending&limit=10\n\n";

echo "   Get job statistics:\n";
echo "   GET /api/jobs/stats\n\n";

echo "   Retry a failed job:\n";
echo "   POST /api/jobs/{id}/retry\n\n";

echo "6. Worker Command Usage:\n";
echo "   To start the worker process:\n";
echo "   php bin/console worker:run\n\n";
echo "   Worker options:\n";
echo "   --once           Process one job and exit\n";
echo "   --jobs=10        Process maximum 10 jobs\n";
echo "   --timeout=3600   Run for maximum 1 hour\n";
echo "   --sleep=3        Sleep 3 seconds when no jobs available\n";
echo "   --max-memory=128 Maximum memory usage in MB\n\n";

echo "=== Test completed ===\n";
echo "Check the error log for detailed job processing output.\n";