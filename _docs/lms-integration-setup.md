# LMS Integration Service Setup Guide

## Overview

The LMS Integration Service provides a unified interface for integrating with various Learning Management Systems and AI platforms. This service supports:

- **Google Gemini API** - Advanced AI content generation and analysis
- **Ollama** - Local LLM for privacy-focused AI processing
- **Tencent Education Cloud** - Chinese LMS platform with comprehensive course management

## Prerequisites

- Docker and Docker Compose
- PHP 8.1 or higher
- Composer
- Valid API keys for external services (Gemini, Tencent)

## Installation

### 1. Docker Setup

The service includes Ollama in the Docker configuration for local testing. Start the services:

```bash
docker-compose up -d
```

This will start:
- Main application container
- MySQL database
- Redis cache
- Ollama LLM service
- MailHog for email testing

### 2. Ollama Model Setup

After starting Docker, you need to pull the required models:

```bash
# Pull the default Llama2 model
docker-compose exec ollama ollama pull llama2

# Or pull other models as needed
docker-compose exec ollama ollama pull codellama
docker-compose exec ollama ollama pull mistral
```

Available models can be found at: https://ollama.ai/library

### 3. Environment Configuration

Add the following configuration to your `.env` file:

```env
# LMS Service Configuration
LMS_GEMINI_ENABLED=false
LMS_GEMINI_API_KEY=your_gemini_api_key_here
LMS_GEMINI_MODEL=gemini-pro

LMS_OLLAMA_ENABLED=true
LMS_OLLAMA_HOST=http://ollama:11434
LMS_OLLAMA_MODEL=llama2

LMS_TENCENT_EDU_ENABLED=false
LMS_TENCENT_EDU_APP_ID=your_tencent_app_id
LMS_TENCENT_EDU_SECRET_KEY=your_tencent_secret_key
LMS_TENCENT_EDU_REGION=ap-beijing

# Docker port forwarding (optional)
FORWARD_OLLAMA_PORT=11434
```

## API Keys Setup

### Google Gemini API

1. Go to [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Create a new API key
3. Add the key to your `.env` file as `LMS_GEMINI_API_KEY`
4. Set `LMS_GEMINI_ENABLED=true`

### Tencent Education Cloud

1. Register at [Tencent Cloud](https://cloud.tencent.com/)
2. Enable the Education services
3. Get your App ID and Secret Key from the console
4. Add credentials to your `.env` file
5. Set `LMS_TENCENT_EDU_ENABLED=true`

## Usage Examples

### Basic Service Usage

```php
<?php
use IamLab\Service\LMS\LMSService;

// Initialize the service
$lmsService = new LMSService();
$lmsService->initialize();

// Generate content using Ollama (local)
$result = $lmsService->generateContent(
    "Create a lesson plan about PHP programming basics",
    'ollama',
    ['max_tokens' => 1000]
);

// Generate content using Gemini
$result = $lmsService->generateContent(
    "Analyze this educational text for readability",
    'gemini',
    ['temperature' => 0.7]
);

// Create a course using Tencent Education
$courseData = [
    'title' => 'Introduction to Web Development',
    'description' => 'Learn the basics of HTML, CSS, and JavaScript',
    'teacher_id' => 'teacher_123',
    'start_time' => time(),
    'end_time' => time() + 3600
];

$result = $lmsService->createCourse($courseData, 'tencent_edu');
```

### Content Generation

```php
// Generate lesson content
$lesson = $lmsService->generateContent(
    "Create a comprehensive lesson about database design",
    'ollama',
    [
        'max_tokens' => 2000,
        'temperature' => 0.8
    ]
);

// Generate quiz content
$quiz = $lmsService->generateContent(
    "Create 5 multiple choice questions about JavaScript arrays",
    'gemini',
    [
        'content_type' => 'quiz',
        'question_count' => 5,
        'difficulty' => 'intermediate'
    ]
);
```

### Text Analysis

```php
// Analyze educational content
$analysis = $lmsService->analyzeText(
    "Your educational content here...",
    'gemini',
    ['type' => 'educational']
);

// Sentiment analysis
$sentiment = $lmsService->analyzeText(
    "Student feedback text...",
    'ollama',
    ['type' => 'sentiment']
);
```

### Course Management (Tencent Education)

```php
// Create a live course
$courseData = [
    'title' => 'Live Programming Workshop',
    'description' => 'Interactive coding session',
    'teacher_id' => 'instructor_001',
    'start_time' => strtotime('+1 hour'),
    'end_time' => strtotime('+3 hours'),
    'max_mic_number' => 5,
    'resolution' => 2 // HD quality
];

$course = $lmsService->createCourse($courseData, 'tencent_edu');

if ($course['success']) {
    echo "Course created with ID: " . $course['course_id'];
    echo "Teacher URL: " . $course['teacher_url'];
    echo "Student URL: " . $course['student_url'];
}
```

## Service Status and Health Checks

```php
// Check which integrations are available
$available = $lmsService->getAvailableIntegrations();
// Returns: ['ollama', 'gemini', 'tencent_edu'] (based on configuration)

// Check integration health
$status = $lmsService->getIntegrationStatus();
/*
Returns:
[
    'ollama' => [
        'enabled' => true,
        'healthy' => true,
        'config' => [...]
    ],
    'gemini' => [
        'enabled' => true,
        'healthy' => true,
        'config' => [...]
    ]
]
*/

// Check specific integration
$isAvailable = $lmsService->isIntegrationAvailable('ollama');
```

## Troubleshooting

### Ollama Issues

1. **Service not starting**: Check Docker logs
   ```bash
   docker-compose logs ollama
   ```

2. **Model not found**: Pull the required model
   ```bash
   docker-compose exec ollama ollama pull llama2
   ```

3. **Connection timeout**: Increase timeout in integration config
   ```php
   'timeout' => 120 // seconds
   ```

### Gemini API Issues

1. **Invalid API key**: Verify key in Google AI Studio
2. **Rate limiting**: Implement request throttling
3. **Model not available**: Check supported models in documentation

### Tencent Education Issues

1. **Authentication failed**: Verify App ID and Secret Key
2. **Region issues**: Ensure correct region setting
3. **API limits**: Check your Tencent Cloud quotas

## Performance Optimization

### Ollama Performance

- Use appropriate model sizes for your hardware
- Consider GPU acceleration for better performance
- Cache frequently used prompts

### API Rate Limiting

- Implement caching for repeated requests
- Use appropriate timeout values
- Consider request queuing for high-volume usage

### Memory Management

- Monitor memory usage with large models
- Implement proper error handling
- Use streaming for long responses when available

## Security Considerations

1. **API Keys**: Store securely, never commit to version control
2. **Local Processing**: Ollama keeps data local for privacy
3. **Input Validation**: Always validate user inputs before processing
4. **Rate Limiting**: Implement to prevent abuse
5. **Logging**: Log requests but avoid sensitive data

## Advanced Configuration

### Custom Model Configuration

```env
# Use different models for different purposes
LMS_OLLAMA_MODEL_CONTENT=llama2
LMS_OLLAMA_MODEL_CODE=codellama
LMS_OLLAMA_MODEL_ANALYSIS=mistral
```

### Integration Priorities

Configure fallback integrations:

```php
$priorities = ['ollama', 'gemini', 'tencent_edu'];
foreach ($priorities as $integration) {
    if ($lmsService->isIntegrationAvailable($integration)) {
        $result = $lmsService->generateContent($prompt, $integration);
        if ($result['success']) break;
    }
}
```

## Support and Resources

- **Ollama Documentation**: https://ollama.ai/
- **Google Gemini API**: https://ai.google.dev/
- **Tencent Cloud Education**: https://cloud.tencent.com/product/lcic
- **Project Issues**: Create issues in the project repository

## Version History

- **v1.0.0** - Initial release with Gemini, Ollama, and Tencent Education support
- **v1.1.0** - Added advanced text analysis and course management features
- **v1.2.0** - Enhanced Docker configuration and health checks