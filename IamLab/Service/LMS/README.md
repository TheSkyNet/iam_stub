# LMS Integration Service

## Overview

The LMS Integration Service provides a unified interface for integrating with various Learning Management Systems and AI platforms. This service is designed to be solid and considerable for the framework, offering seamless integration with multiple platforms through a consistent API.

## Architecture

```
IamLab/Service/LMS/
├── LMSService.php                 # Main service class
├── Integrations/
│   ├── LMSIntegrationInterface.php # Base interface
│   ├── GeminiIntegration.php      # Google Gemini API
│   ├── OllamaIntegration.php      # Local Ollama LLM
│   └── TencentEduIntegration.php  # Tencent Education Cloud
└── README.md                      # This file
```

## Supported Integrations

### 1. Google Gemini API
- **Purpose**: Advanced AI content generation and text analysis
- **Features**: 
  - Content generation with customizable parameters
  - Text analysis and sentiment detection
  - Course outline generation
  - Multi-language support
- **Configuration**: Requires API key from Google AI Studio

### 2. Ollama (Local LLM)
- **Purpose**: Privacy-focused local AI processing
- **Features**:
  - Local content generation
  - No data leaves your infrastructure
  - Multiple model support (Llama2, CodeLlama, Mistral, etc.)
  - Model management capabilities
- **Configuration**: Runs in Docker container, no external API keys needed

### 3. Tencent Education Cloud
- **Purpose**: Chinese LMS platform with comprehensive course management
- **Features**:
  - Live course creation and management
  - Video conferencing integration
  - Real-time collaboration tools
  - Student and teacher management
  - Recording and analytics
- **Configuration**: Requires Tencent Cloud credentials

## Service Interface

### Main Methods

#### `generateContent(string $prompt, string $integration = 'ollama', array $options = []): array`
Generate content using the specified integration.

**Parameters:**
- `$prompt`: The input prompt for content generation
- `$integration`: Integration to use ('gemini', 'ollama', 'tencent_edu')
- `$options`: Additional options (temperature, max_tokens, etc.)

**Returns:**
```php
[
    'success' => true|false,
    'content' => 'generated content',
    'model' => 'model_used',
    'usage' => [...] // Usage statistics if available
]
```

#### `createCourse(array $courseData, string $integration = 'tencent_edu'): array`
Create a course using the specified LMS integration.

**Parameters:**
- `$courseData`: Course information (title, description, teacher_id, etc.)
- `$integration`: LMS integration to use

**Returns:**
```php
[
    'success' => true|false,
    'course_id' => 'generated_course_id',
    'course_url' => 'course_access_url',
    'teacher_url' => 'teacher_access_url',
    'student_url' => 'student_access_url'
]
```

#### `analyzeText(string $text, string $integration = 'gemini', array $options = []): array`
Analyze text content using AI capabilities.

**Parameters:**
- `$text`: Text to analyze
- `$integration`: Integration to use for analysis
- `$options`: Analysis options (type: 'general', 'educational', 'sentiment', 'summary')

#### `getAvailableIntegrations(): array`
Get list of currently available and enabled integrations.

#### `isIntegrationAvailable(string $integration): bool`
Check if a specific integration is available and healthy.

#### `getIntegrationStatus(): array`
Get detailed status information for all integrations.

## Usage Examples

### Basic Content Generation

```php
use IamLab\Service\LMS\LMSService;

$lmsService = new LMSService();
$lmsService->initialize();

// Generate a lesson plan using local Ollama
$result = $lmsService->generateContent(
    "Create a lesson plan for teaching PHP arrays to beginners",
    'ollama',
    [
        'max_tokens' => 1500,
        'temperature' => 0.7
    ]
);

if ($result['success']) {
    echo $result['content'];
}
```

### Course Creation

```php
// Create a live course using Tencent Education
$courseData = [
    'title' => 'Advanced PHP Programming',
    'description' => 'Deep dive into PHP advanced concepts',
    'teacher_id' => 'instructor_001',
    'start_time' => strtotime('+1 day'),
    'end_time' => strtotime('+1 day +2 hours'),
    'max_mic_number' => 10
];

$course = $lmsService->createCourse($courseData, 'tencent_edu');

if ($course['success']) {
    // Course created successfully
    $courseId = $course['course_id'];
    $teacherUrl = $course['teacher_url'];
    $studentUrl = $course['student_url'];
}
```

### Text Analysis

```php
// Analyze educational content
$content = "This lesson covers the fundamentals of object-oriented programming...";

$analysis = $lmsService->analyzeText(
    $content,
    'gemini',
    ['type' => 'educational']
);

if ($analysis['success']) {
    $readabilityScore = $analysis['readability_score'];
    $complexity = $analysis['educational_metrics']['complexity_level'];
    $engagement = $analysis['educational_metrics']['engagement_potential'];
}
```

### Health Monitoring

```php
// Check service health
$status = $lmsService->getIntegrationStatus();

foreach ($status as $integration => $info) {
    echo "{$integration}: " . ($info['healthy'] ? 'Healthy' : 'Unhealthy') . "\n";
}

// Check specific integration
if ($lmsService->isIntegrationAvailable('ollama')) {
    // Use Ollama for processing
}
```

## Configuration

The service uses the framework's configuration system. Add these settings to your configuration:

```php
// config/lms.php
return [
    'gemini' => [
        'enabled' => env('LMS_GEMINI_ENABLED', false),
        'api_key' => env('LMS_GEMINI_API_KEY', ''),
        'model' => env('LMS_GEMINI_MODEL', 'gemini-pro'),
    ],
    'ollama' => [
        'enabled' => env('LMS_OLLAMA_ENABLED', true),
        'host' => env('LMS_OLLAMA_HOST', 'http://ollama:11434'),
        'model' => env('LMS_OLLAMA_MODEL', 'llama2'),
    ],
    'tencent_edu' => [
        'enabled' => env('LMS_TENCENT_EDU_ENABLED', false),
        'app_id' => env('LMS_TENCENT_EDU_APP_ID', ''),
        'secret_key' => env('LMS_TENCENT_EDU_SECRET_KEY', ''),
        'region' => env('LMS_TENCENT_EDU_REGION', 'ap-beijing'),
    ]
];
```

## Error Handling

All methods return arrays with a `success` boolean field. On failure, an `error` field contains the error message:

```php
$result = $lmsService->generateContent("Test prompt", 'invalid_integration');

if (!$result['success']) {
    echo "Error: " . $result['error'];
    // Handle error appropriately
}
```

## Integration Capabilities

Each integration has different capabilities. Use `getCapabilities()` to check:

```php
$integration = new OllamaIntegration($config);
$capabilities = $integration->getCapabilities();

if ($capabilities['local_processing']) {
    // This integration processes data locally
}

if ($capabilities['supports_code']) {
    // This integration can handle code-related tasks
}
```

## Performance Considerations

### Ollama
- Model loading can take time on first request
- Larger models require more memory and processing time
- Consider model caching strategies

### Gemini API
- Subject to rate limiting
- Network latency affects response time
- Implement proper retry logic

### Tencent Education
- Real-time features require stable connection
- Consider regional deployment for better performance

## Security

### API Keys
- Store API keys securely in environment variables
- Never commit API keys to version control
- Rotate keys regularly

### Input Validation
- Always validate user inputs before processing
- Sanitize prompts to prevent injection attacks
- Implement rate limiting to prevent abuse

### Data Privacy
- Ollama processes data locally for maximum privacy
- External APIs may store or process data according to their policies
- Review privacy policies of external services

## Extending the Service

### Adding New Integrations

1. Create a new class implementing `LMSIntegrationInterface`
2. Add configuration options
3. Update the main `LMSService` class to include the new integration
4. Add tests and documentation

Example:

```php
class NewLMSIntegration implements LMSIntegrationInterface
{
    public function __construct(array $config) { /* ... */ }
    public function generateContent(string $prompt, array $options = []): array { /* ... */ }
    public function createCourse(array $courseData): array { /* ... */ }
    public function analyzeText(string $text, array $options = []): array { /* ... */ }
    public function healthCheck(): bool { /* ... */ }
    public function getCapabilities(): array { /* ... */ }
}
```

### Custom Content Types

Extend integrations to support custom content types by modifying the `generateContent` method options:

```php
$result = $lmsService->generateContent(
    "Create assessment rubric",
    'gemini',
    [
        'content_type' => 'rubric',
        'criteria_count' => 5,
        'point_scale' => 4
    ]
);
```

## Testing

The service includes comprehensive error handling and health checks. For testing:

1. Use Ollama for local testing without external dependencies
2. Mock external API responses for unit tests
3. Test error conditions and fallback scenarios
4. Verify configuration loading and validation

## Troubleshooting

### Common Issues

1. **Integration not available**: Check configuration and health status
2. **Timeout errors**: Increase timeout values or check network connectivity
3. **Authentication failures**: Verify API keys and credentials
4. **Model not found**: Ensure required models are installed (Ollama)

### Debug Mode

Enable verbose logging by setting debug options:

```php
$result = $lmsService->generateContent(
    $prompt,
    'ollama',
    ['debug' => true]
);
```

## Support

For issues and feature requests:
1. Check the setup documentation in `_docs/lms-integration-setup.md`
2. Review integration-specific documentation
3. Create issues in the project repository
4. Consult the framework documentation

## Version Compatibility

- **PHP**: 8.1+
- **Phalcon**: 5.0+
- **Docker**: 20.0+
- **Ollama**: Latest stable
- **External APIs**: Current versions as of implementation date