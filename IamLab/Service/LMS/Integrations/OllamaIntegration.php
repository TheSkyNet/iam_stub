<?php

namespace IamLab\Service\LMS\Integrations;

/**
 * Ollama Integration
 * 
 * Integrates with Ollama for local LLM support
 */
class OllamaIntegration implements LMSIntegrationInterface
{
    private array $config;
    private string $host;
    private string $model;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->host = rtrim($config['host'] ?? 'http://ollama:11434', '/');
        $this->model = $config['model'] ?? 'llama2';
    }

    public function generateContent(string $prompt, array $options = []): array
    {
        $url = $this->host . '/api/generate';
        
        $data = [
            'model' => $this->model,
            'prompt' => $prompt,
            'stream' => false,
            'options' => [
                'temperature' => $options['temperature'] ?? 0.7,
                'top_k' => $options['top_k'] ?? 40,
                'top_p' => $options['top_p'] ?? 0.9,
                'num_predict' => $options['max_tokens'] ?? 1024,
            ]
        ];

        try {
            $response = $this->makeRequest($url, $data);
            
            if (isset($response['response'])) {
                return [
                    'success' => true,
                    'content' => $response['response'],
                    'model' => $this->model,
                    'done' => $response['done'] ?? false,
                    'context' => $response['context'] ?? null,
                    'total_duration' => $response['total_duration'] ?? null,
                    'load_duration' => $response['load_duration'] ?? null,
                    'prompt_eval_count' => $response['prompt_eval_count'] ?? null,
                    'eval_count' => $response['eval_count'] ?? null
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
        $prompt = "Create a comprehensive course outline for: " . ($courseData['title'] ?? 'Untitled Course') . "\n";
        $prompt .= "Description: " . ($courseData['description'] ?? '') . "\n";
        $prompt .= "Target audience: " . ($courseData['audience'] ?? 'General') . "\n";
        $prompt .= "Duration: " . ($courseData['duration'] ?? 'Not specified') . "\n\n";
        $prompt .= "Please provide a detailed course structure with:\n";
        $prompt .= "1. Course overview and objectives\n";
        $prompt .= "2. Module breakdown with topics\n";
        $prompt .= "3. Learning outcomes for each module\n";
        $prompt .= "4. Assessment methods\n";
        $prompt .= "5. Required resources\n\n";
        $prompt .= "Format the response in a clear, structured manner.";

        $result = $this->generateContent($prompt);
        
        if ($result['success']) {
            return [
                'success' => true,
                'course_outline' => $result['content'],
                'generated_by' => 'ollama',
                'model' => $this->model,
                'original_data' => $courseData,
                'generation_stats' => [
                    'total_duration' => $result['total_duration'] ?? null,
                    'eval_count' => $result['eval_count'] ?? null
                ]
            ];
        }

        return $result;
    }

    public function analyzeText(string $text, array $options = []): array
    {
        $analysisType = $options['type'] ?? 'general';
        
        $prompts = [
            'general' => "Analyze the following text and provide insights about its content, structure, tone, and key themes. Be specific and detailed:\n\n",
            'educational' => "Analyze this educational content and provide detailed feedback on:\n1. Clarity and readability\n2. Structure and organization\n3. Learning effectiveness\n4. Areas for improvement\n5. Strengths\n\nText to analyze:\n\n",
            'sentiment' => "Perform a detailed sentiment analysis on the following text. Categorize the emotional tone, identify key sentiment indicators, and provide confidence scores:\n\n",
            'summary' => "Provide a comprehensive summary of the following text. Include:\n1. Main points and key arguments\n2. Supporting details\n3. Conclusions\n4. Important context\n\nText to summarize:\n\n",
            'grammar' => "Review the following text for grammar, spelling, and style issues. Provide specific corrections and suggestions:\n\n"
        ];

        $prompt = ($prompts[$analysisType] ?? $prompts['general']) . $text;

        return $this->generateContent($prompt, $options);
    }

    public function healthCheck(): bool
    {
        try {
            // First check if Ollama is running
            $url = $this->host . '/api/tags';
            $response = $this->makeRequest($url, [], 'GET');
            
            if (!isset($response['models'])) {
                return false;
            }

            // Check if our model is available
            $modelExists = false;
            foreach ($response['models'] as $model) {
                if ($model['name'] === $this->model || strpos($model['name'], $this->model) === 0) {
                    $modelExists = true;
                    break;
                }
            }

            if (!$modelExists) {
                return false;
            }

            // Test generation
            $result = $this->generateContent("Hello", ['max_tokens' => 10]);
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
            'course_creation' => true,
            'real_time' => true,
            'local_processing' => true,
            'privacy_focused' => true,
            'offline_capable' => true,
            'languages' => ['en', 'es', 'fr', 'de', 'it', 'pt', 'ru', 'ja', 'ko', 'zh', 'ar'],
            'supports_code' => true,
            'supports_reasoning' => true,
            'customizable' => true
        ];
    }

    /**
     * Get available models from Ollama
     */
    public function getAvailableModels(): array
    {
        try {
            $url = $this->host . '/api/tags';
            $response = $this->makeRequest($url, [], 'GET');
            
            if (isset($response['models'])) {
                return array_map(function($model) {
                    return [
                        'name' => $model['name'],
                        'size' => $model['size'] ?? null,
                        'modified_at' => $model['modified_at'] ?? null
                    ];
                }, $response['models']);
            }

            return [];
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Pull a model from Ollama registry
     */
    public function pullModel(string $modelName): array
    {
        $url = $this->host . '/api/pull';
        $data = ['name' => $modelName];

        try {
            $response = $this->makeRequest($url, $data);
            return [
                'success' => true,
                'status' => $response['status'] ?? 'unknown',
                'response' => $response
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    private function makeRequest(string $url, array $data = [], string $method = 'POST'): array
    {
        $ch = curl_init();
        
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 120, // Longer timeout for local LLM
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = json_encode($data);
        }

        curl_setopt_array($ch, $options);

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