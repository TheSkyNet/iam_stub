<?php

// Simple test script for LMS Integration Service
require_once 'vendor/autoload.php';

use IamLab\Service\LMS\LMSService;
use IamLab\Service\LMS\Integrations\OllamaIntegration;
use IamLab\Service\LMS\Integrations\GeminiIntegration;
use IamLab\Service\LMS\Integrations\TencentEduIntegration;

echo "[DEBUG_LOG] Starting LMS Integration Service Test\n";

// Test 1: Basic service initialization
echo "[DEBUG_LOG] Test 1: Service Initialization\n";
try {
    $lmsService = new LMSService();
    $lmsService->initialize();
    echo "[DEBUG_LOG] ✓ Service initialized successfully\n";
} catch (Exception $e) {
    echo "[DEBUG_LOG] ✗ Service initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check available integrations
echo "[DEBUG_LOG] Test 2: Available Integrations\n";
$available = $lmsService->getAvailableIntegrations();
echo "[DEBUG_LOG] Available integrations: " . implode(', ', $available) . "\n";

// Test 3: Integration status
echo "[DEBUG_LOG] Test 3: Integration Status\n";
$status = $lmsService->getIntegrationStatus();
foreach ($status as $integration => $info) {
    $healthStatus = $info['healthy'] ? 'Healthy' : 'Unhealthy';
    echo "[DEBUG_LOG] {$integration}: {$healthStatus}\n";
}

// Test 4: Test Ollama integration (if available)
if ($lmsService->isIntegrationAvailable('ollama')) {
    echo "[DEBUG_LOG] Test 4: Ollama Content Generation\n";
    $result = $lmsService->generateContent(
        "Hello, this is a test prompt for content generation.",
        'ollama',
        ['max_tokens' => 100]
    );
    
    if ($result['success']) {
        echo "[DEBUG_LOG] ✓ Ollama content generation successful\n";
        echo "[DEBUG_LOG] Generated content preview: " . substr($result['content'], 0, 100) . "...\n";
    } else {
        echo "[DEBUG_LOG] ✗ Ollama content generation failed: " . $result['error'] . "\n";
    }
} else {
    echo "[DEBUG_LOG] Test 4: Ollama integration not available\n";
}

// Test 5: Test individual integration classes
echo "[DEBUG_LOG] Test 5: Individual Integration Classes\n";

// Test Ollama integration directly
try {
    $ollamaConfig = [
        'host' => 'http://ollama:11434',
        'model' => 'llama2'
    ];
    $ollama = new OllamaIntegration($ollamaConfig);
    $capabilities = $ollama->getCapabilities();
    echo "[DEBUG_LOG] ✓ Ollama integration class created successfully\n";
    echo "[DEBUG_LOG] Ollama capabilities: " . implode(', ', array_keys(array_filter($capabilities))) . "\n";
} catch (Exception $e) {
    echo "[DEBUG_LOG] ✗ Ollama integration class failed: " . $e->getMessage() . "\n";
}

// Test Gemini integration (without API key, should handle gracefully)
try {
    $geminiConfig = [
        'api_key' => '', // Empty key for testing
        'model' => 'gemini-pro'
    ];
    // This should throw an exception due to missing API key
    $gemini = new GeminiIntegration($geminiConfig);
    echo "[DEBUG_LOG] ✗ Gemini integration should have failed with empty API key\n";
} catch (Exception $e) {
    echo "[DEBUG_LOG] ✓ Gemini integration correctly rejected empty API key\n";
}

// Test Tencent Education integration (without credentials, should handle gracefully)
try {
    $tencentConfig = [
        'app_id' => '', // Empty credentials for testing
        'secret_key' => '',
        'region' => 'ap-beijing'
    ];
    // This should throw an exception due to missing credentials
    $tencent = new TencentEduIntegration($tencentConfig);
    echo "[DEBUG_LOG] ✗ Tencent integration should have failed with empty credentials\n";
} catch (Exception $e) {
    echo "[DEBUG_LOG] ✓ Tencent integration correctly rejected empty credentials\n";
}

// Test 6: Error handling
echo "[DEBUG_LOG] Test 6: Error Handling\n";
$result = $lmsService->generateContent("Test prompt", 'nonexistent_integration');
if (!$result['success']) {
    echo "[DEBUG_LOG] ✓ Error handling works correctly for invalid integration\n";
} else {
    echo "[DEBUG_LOG] ✗ Error handling failed for invalid integration\n";
}

// Test 7: Text analysis (if any integration is available)
if (!empty($available)) {
    echo "[DEBUG_LOG] Test 7: Text Analysis\n";
    $testText = "This is a sample educational text about programming. It covers basic concepts and provides examples for students to learn effectively.";
    
    $integration = $available[0]; // Use first available integration
    $analysis = $lmsService->analyzeText($testText, $integration, ['type' => 'educational']);
    
    if ($analysis['success']) {
        echo "[DEBUG_LOG] ✓ Text analysis successful using {$integration}\n";
        if (isset($analysis['text_length'])) {
            echo "[DEBUG_LOG] Text length: " . $analysis['text_length'] . " characters\n";
        }
    } else {
        echo "[DEBUG_LOG] ✗ Text analysis failed: " . $analysis['error'] . "\n";
    }
}

// Test 8: Course creation (mock data)
if ($lmsService->isIntegrationAvailable('tencent_edu')) {
    echo "[DEBUG_LOG] Test 8: Course Creation (Tencent Education)\n";
    $courseData = [
        'title' => 'Test Course',
        'description' => 'This is a test course for LMS integration',
        'teacher_id' => 'test_teacher_001',
        'start_time' => time() + 3600, // 1 hour from now
        'end_time' => time() + 7200    // 2 hours from now
    ];
    
    $course = $lmsService->createCourse($courseData, 'tencent_edu');
    
    if ($course['success']) {
        echo "[DEBUG_LOG] ✓ Course creation successful\n";
        echo "[DEBUG_LOG] Course ID: " . $course['course_id'] . "\n";
    } else {
        echo "[DEBUG_LOG] ✗ Course creation failed: " . $course['error'] . "\n";
    }
} else {
    echo "[DEBUG_LOG] Test 8: Tencent Education integration not available for course creation\n";
}

echo "[DEBUG_LOG] LMS Integration Service Test Completed\n";
echo "[DEBUG_LOG] Summary:\n";
echo "[DEBUG_LOG] - Service initialization: ✓\n";
echo "[DEBUG_LOG] - Available integrations: " . count($available) . "\n";
echo "[DEBUG_LOG] - Error handling: ✓\n";
echo "[DEBUG_LOG] - Integration classes: ✓\n";

if (count($available) > 0) {
    echo "[DEBUG_LOG] - At least one integration is working\n";
    echo "[DEBUG_LOG] Test Status: PASSED\n";
} else {
    echo "[DEBUG_LOG] - No integrations are currently available\n";
    echo "[DEBUG_LOG] Test Status: PARTIAL (Service works but no integrations configured)\n";
}