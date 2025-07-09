<?php

namespace IamLab\Service\LMS\Integrations;

/**
 * Google Gemini Integration
 * 
 * Integrates with Google's Gemini AI API for content generation and analysis
 */
class GeminiIntegration implements LMSIntegrationInterface
{
    private array $config;
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->apiKey = $config['api_key'] ?? '';
        $this->model = $config['model'] ?? 'gemini-pro';

        if (empty($this->apiKey)) {
            throw new \InvalidArgumentException('Gemini API key is required');
        }
    }

    public function generateContent(string $prompt, array $options = []): array
    {
        $url = $this->baseUrl . $this->model . ':generateContent?key=' . $this->apiKey;
        
        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'topK' => $options['top_k'] ?? 40,
                'topP' => $options['top_p'] ?? 0.95,
                'maxOutputTokens' => $options['max_tokens'] ?? 1024,
            ]
        ];

        try {
            $response = $this->makeRequest($url, $data);
            
            if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
                return [
                    'success' => true,
                    'content' => $response['candidates'][0]['content']['parts'][0]['text'],
                    'model' => $this->model,
                    'usage' => $response['usageMetadata'] ?? null
                ];
            }

            return [
                'success' => false,
                'error' => 'No content generated',
                'response' => $response
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function createCourse(array $courseData): array
    {
        // Gemini doesn't directly support course creation, but we can generate course content
        $prompt = "Create a comprehensive course outline for: " . ($courseData['title'] ?? 'Untitled Course') . "\n";
        $prompt .= "Description: " . ($courseData['description'] ?? '') . "\n";
        $prompt .= "Target audience: " . ($courseData['audience'] ?? 'General') . "\n";
        $prompt .= "Duration: " . ($courseData['duration'] ?? 'Not specified') . "\n\n";
        $prompt .= "Please provide a detailed course structure with modules, lessons, and learning objectives.";

        $result = $this->generateContent($prompt);
        
        if ($result['success']) {
            return [
                'success' => true,
                'course_outline' => $result['content'],
                'generated_by' => 'gemini',
                'original_data' => $courseData
            ];
        }

        return $result;
    }

    public function analyzeText(string $text, array $options = []): array
    {
        $analysisType = $options['type'] ?? 'general';
        
        $prompts = [
            'general' => "Analyze the following text and provide insights about its content, tone, and key themes:\n\n",
            'educational' => "Analyze this educational content and provide feedback on clarity, structure, and learning effectiveness:\n\n",
            'sentiment' => "Perform sentiment analysis on the following text and categorize the emotional tone:\n\n",
            'summary' => "Provide a concise summary of the following text, highlighting the main points:\n\n"
        ];

        $prompt = ($prompts[$analysisType] ?? $prompts['general']) . $text;

        return $this->generateContent($prompt, $options);
    }

    public function healthCheck(): bool
    {
        try {
            $result = $this->generateContent("Hello, this is a health check.", ['max_tokens' => 50]);
            return $result['success'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getCapabilities(): array
    {
        return [
            'content_generation' => true,
            'text_analysis' => true,
            'course_creation' => true, // Via content generation
            'real_time' => true,
            'languages' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh'],
            'max_tokens' => 8192,
            'supports_images' => false, // gemini-pro-vision would support images
            'supports_code' => true
        ];
    }

    private function makeRequest(string $url, array $data): array
    {
        $ch = curl_init();
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            throw new \Exception("cURL error: " . $error);
        }

        if ($httpCode !== 200) {
            throw new \Exception("HTTP error: " . $httpCode . " - " . $response);
        }

        $decoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("JSON decode error: " . json_last_error_msg());
        }

        return $decoded;
    }
}