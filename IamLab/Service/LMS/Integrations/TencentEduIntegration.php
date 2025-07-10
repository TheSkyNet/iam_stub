<?php

namespace IamLab\Service\LMS\Integrations;

use Exception;
use InvalidArgumentException;

/**
 * Tencent Education Cloud Integration
 * 
 * Integrates with Tencent Cloud's Education services for LMS functionality
 * This is a popular Chinese LMS platform with comprehensive course management
 */
class TencentEduIntegration implements LMSIntegrationInterface
{
    private string $appId;
    private string $secretKey;
    private string $region;
    private string $baseUrl;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->appId = $config['app_id'] ?? '';
        $this->secretKey = $config['secret_key'] ?? '';
        $this->region = $config['region'] ?? 'ap-beijing';
        $this->baseUrl = "https://lcic.tencentcloudapi.com";

        if (empty($this->appId) || empty($this->secretKey)) {
            throw new InvalidArgumentException('Tencent Education app_id and secret_key are required');
        }
    }

    public function generateContent(string $prompt, array $options = []): array
    {
        // Tencent Education doesn't have direct content generation, 
        // but we can use their AI services or create structured content
        try {
            $contentType = $options['content_type'] ?? 'lesson';
            
            switch ($contentType) {
                case 'lesson':
                    return $this->generateLessonContent($prompt, $options);
                case 'quiz':
                    return $this->generateQuizContent($prompt, $options);
                case 'assignment':
                    return $this->generateAssignmentContent($prompt, $options);
                default:
                    return $this->generateGenericContent($prompt, $options);
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createCourse(array $courseData): array
    {
        try {
            $action = 'CreateRoom';
            $params = [
                'Name' => $courseData['title'] ?? 'Untitled Course',
                'StartTime' => $courseData['start_time'] ?? time(),
                'EndTime' => $courseData['end_time'] ?? (time() + 3600), // 1 hour default
                'TeacherId' => $courseData['teacher_id'] ?? '',
                'Resolution' => $courseData['resolution'] ?? 1, // 1: 960*720, 2: 1280*720
                'MaxMicNumber' => $courseData['max_mic_number'] ?? 1,
                'SubType' => $courseData['sub_type'] ?? 'videodoc', // videodoc, video, doc
                'TRTCConfig' => [
                    'SdkAppId' => $this->appId,
                    'UserId' => $courseData['teacher_id'] ?? 'teacher_' . time(),
                    'UserSig' => $this->generateUserSig($courseData['teacher_id'] ?? 'teacher_' . time())
                ]
            ];

            if (isset($courseData['description'])) {
                $params['Description'] = $courseData['description'];
            }

            $response = $this->makeRequest($action, $params);

            if (isset($response['Response']['RoomId'])) {
                return [
                    'success' => true,
                    'course_id' => $response['Response']['RoomId'],
                    'course_url' => $this->generateCourseUrl($response['Response']['RoomId']),
                    'teacher_url' => $response['Response']['TeacherUrl'] ?? null,
                    'student_url' => $response['Response']['StudentUrl'] ?? null,
                    'created_by' => 'tencent_edu',
                    'original_data' => $courseData,
                    'response' => $response['Response']
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to create course',
                'response' => $response
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function analyzeText(string $text, array $options = []): array
    {
        // Implement text analysis using Tencent's NLP services
        try {
            $analysisType = $options['type'] ?? 'general';
            
            // This would integrate with Tencent's NLP API
            // For now, we'll provide a structured analysis
            $analysis = [
                'success' => true,
                'analysis_type' => $analysisType,
                'text_length' => strlen($text),
                'word_count' => str_word_count($text),
                'language' => $this->detectLanguage($text),
                'readability_score' => $this->calculateReadabilityScore($text),
                'key_phrases' => $this->extractKeyPhrases($text),
                'sentiment' => $this->analyzeSentiment($text),
                'educational_metrics' => [
                    'complexity_level' => $this->assessComplexity($text),
                    'learning_objectives_clarity' => $this->assessLearningObjectives($text),
                    'engagement_potential' => $this->assessEngagement($text)
                ]
            ];

            return $analysis;

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function healthCheck(): bool
    {
        try {
            $action = 'DescribeRooms';
            $params = [
                'Limit' => 1,
                'Offset' => 0
            ];

            $response = $this->makeRequest($action, $params);
            return isset($response['Response']);

        } catch (Exception $e) {
            return false;
        }
    }

    public function getCapabilities(): array
    {
        return [
            'content_generation' => true, // Limited, structured content
            'text_analysis' => true,
            'course_creation' => true, // Full LMS course creation
            'real_time' => true,
            'video_conferencing' => true,
            'screen_sharing' => true,
            'whiteboard' => true,
            'recording' => true,
            'user_management' => true,
            'analytics' => true,
            'languages' => ['zh', 'en'], // Primarily Chinese
            'regions' => ['ap-beijing', 'ap-shanghai', 'ap-guangzhou', 'ap-chengdu'],
            'max_participants' => 1000,
            'supports_mobile' => true
        ];
    }

    /**
     * Get course/room information
     */
    public function getCourseInfo(string $courseId): array
    {
        try {
            $action = 'DescribeRoom';
            $params = [
                'RoomId' => (int)$courseId
            ];

            $response = $this->makeRequest($action, $params);

            if (isset($response['Response']['RoomInfo'])) {
                return [
                    'success' => true,
                    'course_info' => $response['Response']['RoomInfo']
                ];
            }

            return [
                'success' => false,
                'error' => 'Course not found'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete a course/room
     */
    public function deleteCourse(string $courseId): array
    {
        try {
            $action = 'DeleteRoom';
            $params = [
                'RoomId' => (int)$courseId
            ];

            $response = $this->makeRequest($action, $params);

            return [
                'success' => true,
                'message' => 'Course deleted successfully',
                'response' => $response['Response'] ?? null
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function generateLessonContent(string $prompt, array $options): array
    {
        // Generate structured lesson content
        return [
            'success' => true,
            'content' => [
                'title' => $this->extractTitle($prompt),
                'objectives' => $this->generateObjectives($prompt),
                'content_outline' => $this->generateOutline($prompt),
                'activities' => $this->generateActivities($prompt),
                'assessment' => $this->generateAssessment($prompt)
            ],
            'type' => 'lesson',
            'generated_by' => 'tencent_edu'
        ];
    }

    private function generateQuizContent(string $prompt, array $options): array
    {
        return [
            'success' => true,
            'content' => [
                'questions' => $this->generateQuestions($prompt, $options['question_count'] ?? 5),
                'difficulty' => $options['difficulty'] ?? 'medium',
                'time_limit' => $options['time_limit'] ?? 30
            ],
            'type' => 'quiz',
            'generated_by' => 'tencent_edu'
        ];
    }

    private function generateAssignmentContent(string $prompt, array $options): array
    {
        return [
            'success' => true,
            'content' => [
                'title' => $this->extractTitle($prompt),
                'instructions' => $prompt,
                'requirements' => $this->generateRequirements($prompt),
                'rubric' => $this->generateRubric($prompt),
                'due_date' => $options['due_date'] ?? null
            ],
            'type' => 'assignment',
            'generated_by' => 'tencent_edu'
        ];
    }

    private function generateGenericContent(string $prompt, array $options): array
    {
        return [
            'success' => true,
            'content' => $prompt,
            'type' => 'generic',
            'generated_by' => 'tencent_edu'
        ];
    }

    private function makeRequest(string $action, array $params): array
    {
        $timestamp = time();
        $nonce = rand(10000, 99999);
        
        $headers = [
            'Authorization' => $this->generateAuthHeader($action, $params, $timestamp, $nonce),
            'Content-Type' => 'application/json; charset=utf-8',
            'Host' => 'lcic.tencentcloudapi.com',
            'X-TC-Action' => $action,
            'X-TC-Timestamp' => $timestamp,
            'X-TC-Version' => '2022-08-17',
            'X-TC-Region' => $this->region
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->baseUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array_map(function($key, $value) {
                return "$key: $value";
            }, array_keys($headers), $headers),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new Exception("cURL error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new Exception("HTTP error: " . $httpCode . " - " . $response);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("JSON decode error: " . json_last_error_msg());
        }

        return $decoded;
    }

    private function generateAuthHeader(string $action, array $params, int $timestamp, int $nonce): string
    {
        // Simplified auth header generation for Tencent Cloud API
        // In production, this would implement the full TC3-HMAC-SHA256 signature
        $signature = hash_hmac('sha256', $action . json_encode($params) . $timestamp . $nonce, $this->secretKey);
        return "TC3-HMAC-SHA256 Credential={$this->appId}, SignedHeaders=content-type;host, Signature={$signature}";
    }

    private function generateUserSig(string $userId): string
    {
        // Generate user signature for TRTC
        // This is a simplified version - production would use proper JWT
        return base64_encode(hash_hmac('sha256', $userId . time(), $this->secretKey));
    }

    private function generateCourseUrl(int $roomId): string
    {
        return "https://edu.qq.com/room/{$roomId}";
    }

    // Helper methods for content analysis and generation
    private function detectLanguage(string $text): string
    {
        // Simple language detection
        return preg_match('/[\x{4e00}-\x{9fff}]/u', $text) ? 'zh' : 'en';
    }

    private function calculateReadabilityScore(string $text): float
    {
        // Simplified readability calculation
        $sentences = preg_split('/[.!?]+/', $text);
        $words = str_word_count($text);
        return $words / max(count($sentences), 1);
    }

    private function extractKeyPhrases(string $text): array
    {
        // Simple key phrase extraction
        $words = str_word_count($text, 1);
        return array_slice(array_unique($words), 0, 10);
    }

    private function analyzeSentiment(string $text): array
    {
        // Basic sentiment analysis
        $positive = ['good', 'great', 'excellent', 'amazing', 'wonderful'];
        $negative = ['bad', 'terrible', 'awful', 'horrible', 'poor'];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positive as $word) {
            $positiveCount += substr_count(strtolower($text), $word);
        }
        
        foreach ($negative as $word) {
            $negativeCount += substr_count(strtolower($text), $word);
        }
        
        if ($positiveCount > $negativeCount) {
            $sentiment = 'positive';
        } elseif ($negativeCount > $positiveCount) {
            $sentiment = 'negative';
        } else {
            $sentiment = 'neutral';
        }
        
        return [
            'sentiment' => $sentiment,
            'confidence' => abs($positiveCount - $negativeCount) / max(strlen($text) / 100, 1)
        ];
    }

    private function assessComplexity(string $text): string
    {
        $avgWordLength = strlen($text) / max(str_word_count($text), 1);
        if ($avgWordLength < 4) return 'beginner';
        if ($avgWordLength < 6) return 'intermediate';
        return 'advanced';
    }

    private function assessLearningObjectives(string $text): float
    {
        $objectives = ['learn', 'understand', 'analyze', 'evaluate', 'create'];
        $count = 0;
        foreach ($objectives as $objective) {
            $count += substr_count(strtolower($text), $objective);
        }
        return min($count / 5.0, 1.0);
    }

    private function assessEngagement(string $text): float
    {
        $engaging = ['example', 'practice', 'exercise', 'activity', 'interactive'];
        $count = 0;
        foreach ($engaging as $word) {
            $count += substr_count(strtolower($text), $word);
        }
        return min($count / 3.0, 1.0);
    }

    private function extractTitle(string $prompt): string
    {
        $lines = explode("\n", $prompt);
        return trim($lines[0]);
    }

    private function generateObjectives(string $prompt): array
    {
        return [
            'Students will understand the key concepts presented',
            'Students will be able to apply the knowledge in practical scenarios',
            'Students will demonstrate mastery through assessment'
        ];
    }

    private function generateOutline(string $prompt): array
    {
        return [
            'Introduction',
            'Main Content',
            'Examples and Practice',
            'Summary and Review'
        ];
    }

    private function generateActivities(string $prompt): array
    {
        return [
            'Discussion questions',
            'Hands-on exercises',
            'Group work',
            'Individual reflection'
        ];
    }

    private function generateAssessment(string $prompt): array
    {
        return [
            'type' => 'mixed',
            'components' => ['quiz', 'assignment', 'participation'],
            'weight' => ['40%', '40%', '20%']
        ];
    }

    private function generateQuestions(string $prompt, int $count): array
    {
        $questions = [];
        for ($i = 1; $i <= $count; $i++) {
            $questions[] = [
                'id' => $i,
                'question' => "Question {$i} based on: " . substr($prompt, 0, 50) . "...",
                'type' => 'multiple_choice',
                'options' => ['Option A', 'Option B', 'Option C', 'Option D'],
                'correct_answer' => 'A'
            ];
        }
        return $questions;
    }

    private function generateRequirements(string $prompt): array
    {
        return [
            'Complete all sections of the assignment',
            'Provide detailed explanations',
            'Include relevant examples',
            'Submit in the specified format'
        ];
    }

    private function generateRubric(string $prompt): array
    {
        return [
            'criteria' => [
                'Content Quality' => ['Excellent', 'Good', 'Satisfactory', 'Needs Improvement'],
                'Organization' => ['Excellent', 'Good', 'Satisfactory', 'Needs Improvement'],
                'Creativity' => ['Excellent', 'Good', 'Satisfactory', 'Needs Improvement']
            ],
            'points' => [4, 3, 2, 1]
        ];
    }
}