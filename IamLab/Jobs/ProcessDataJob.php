<?php

namespace IamLab\Jobs;

use Exception;

/**
 * Process Data Job
 * 
 * Example job for processing data/files
 */
class ProcessDataJob
{
    /**
     * Handle the job
     *
     * @param array $payload
     * @return bool|string
     */
    public function handle(array $payload): bool|string
    {
        try {
            // Validate required payload fields
            if (!isset($payload['data_type']) || !isset($payload['data'])) {
                return 'Missing required fields: data_type, data';
            }

            $dataType = $payload['data_type'];
            $data = $payload['data'];
            $options = $payload['options'] ?? [];

            // Process based on data type
            switch ($dataType) {
                case 'csv':
                    return $this->processCsvData($data, $options);
                
                case 'json':
                    return $this->processJsonData($data, $options);
                
                case 'image':
                    return $this->processImageData($data, $options);
                
                case 'text':
                    return $this->processTextData($data, $options);
                
                default:
                    return "Unsupported data type: {$dataType}";
            }

        } catch (Exception $e) {
            return 'Failed to process data: ' . $e->getMessage();
        }
    }

    /**
     * Process CSV data
     *
     * @param mixed $data
     * @param array $options
     * @return bool|string
     */
    protected function processCsvData($data, array $options): bool|string
    {
        try {
            // Simulate CSV processing
            $rows = is_array($data) ? $data : [];
            $processedCount = 0;

            foreach ($rows as $row) {
                // Simulate row processing
                $this->processRow($row);
                $processedCount++;
                
                // Simulate processing time
                usleep(100000); // 0.1 seconds per row
            }

            error_log("Processed {$processedCount} CSV rows");
            return true;

        } catch (Exception $e) {
            return "CSV processing failed: " . $e->getMessage();
        }
    }

    /**
     * Process JSON data
     *
     * @param mixed $data
     * @param array $options
     * @return bool|string
     */
    protected function processJsonData($data, array $options): bool|string
    {
        try {
            // Simulate JSON processing
            if (is_string($data)) {
                $data = json_decode($data, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return "Invalid JSON data: " . json_last_error_msg();
                }
            }

            $itemCount = is_array($data) ? count($data) : 1;
            
            // Simulate processing time based on data size
            sleep(max(1, intval($itemCount / 100)));

            error_log("Processed JSON data with {$itemCount} items");
            return true;

        } catch (Exception $e) {
            return "JSON processing failed: " . $e->getMessage();
        }
    }

    /**
     * Process image data
     *
     * @param mixed $data
     * @param array $options
     * @return bool|string
     */
    protected function processImageData($data, array $options): bool|string
    {
        try {
            $imagePath = $data['path'] ?? null;
            $operations = $options['operations'] ?? ['resize'];

            if (!$imagePath) {
                return "Image path is required";
            }

            // Simulate image processing operations
            foreach ($operations as $operation) {
                switch ($operation) {
                    case 'resize':
                        $this->simulateImageResize($imagePath, $options);
                        break;
                    
                    case 'compress':
                        $this->simulateImageCompress($imagePath, $options);
                        break;
                    
                    case 'watermark':
                        $this->simulateImageWatermark($imagePath, $options);
                        break;
                }
                
                // Simulate processing time
                sleep(2);
            }

            error_log("Processed image: {$imagePath} with operations: " . implode(', ', $operations));
            return true;

        } catch (Exception $e) {
            return "Image processing failed: " . $e->getMessage();
        }
    }

    /**
     * Process text data
     *
     * @param mixed $data
     * @param array $options
     * @return bool|string
     */
    protected function processTextData($data, array $options): bool|string
    {
        try {
            $text = is_string($data) ? $data : (string)$data;
            $operations = $options['operations'] ?? ['analyze'];

            $results = [];

            foreach ($operations as $operation) {
                switch ($operation) {
                    case 'analyze':
                        $results['word_count'] = str_word_count($text);
                        $results['char_count'] = strlen($text);
                        break;
                    
                    case 'sentiment':
                        $results['sentiment'] = $this->simulateSentimentAnalysis($text);
                        break;
                    
                    case 'keywords':
                        $results['keywords'] = $this->simulateKeywordExtraction($text);
                        break;
                }
            }

            // Simulate processing time
            sleep(1);

            error_log("Processed text data: " . json_encode($results));
            return true;

        } catch (Exception $e) {
            return "Text processing failed: " . $e->getMessage();
        }
    }

    /**
     * Process a single row (helper method)
     *
     * @param mixed $row
     */
    protected function processRow($row): void
    {
        // Simulate row processing logic
        // In a real implementation, this might involve database operations,
        // data validation, transformation, etc.
    }

    /**
     * Simulate image resize operation
     *
     * @param string $imagePath
     * @param array $options
     */
    protected function simulateImageResize(string $imagePath, array $options): void
    {
        $width = $options['width'] ?? 800;
        $height = $options['height'] ?? 600;
        error_log("Simulating image resize: {$imagePath} to {$width}x{$height}");
    }

    /**
     * Simulate image compress operation
     *
     * @param string $imagePath
     * @param array $options
     */
    protected function simulateImageCompress(string $imagePath, array $options): void
    {
        $quality = $options['quality'] ?? 80;
        error_log("Simulating image compression: {$imagePath} at {$quality}% quality");
    }

    /**
     * Simulate image watermark operation
     *
     * @param string $imagePath
     * @param array $options
     */
    protected function simulateImageWatermark(string $imagePath, array $options): void
    {
        $watermark = $options['watermark'] ?? 'default.png';
        error_log("Simulating watermark application: {$imagePath} with {$watermark}");
    }

    /**
     * Simulate sentiment analysis
     *
     * @param string $text
     * @return string
     */
    protected function simulateSentimentAnalysis(string $text): string
    {
        // Simple simulation based on text length and content
        $positiveWords = ['good', 'great', 'excellent', 'amazing', 'wonderful'];
        $negativeWords = ['bad', 'terrible', 'awful', 'horrible', 'disappointing'];
        
        $positiveCount = 0;
        $negativeCount = 0;
        
        foreach ($positiveWords as $word) {
            $positiveCount += substr_count(strtolower($text), $word);
        }
        
        foreach ($negativeWords as $word) {
            $negativeCount += substr_count(strtolower($text), $word);
        }
        
        if ($positiveCount > $negativeCount) {
            return 'positive';
        } elseif ($negativeCount > $positiveCount) {
            return 'negative';
        } else {
            return 'neutral';
        }
    }

    /**
     * Simulate keyword extraction
     *
     * @param string $text
     * @return array
     */
    protected function simulateKeywordExtraction(string $text): array
    {
        // Simple simulation - extract words longer than 4 characters
        $words = str_word_count(strtolower($text), 1);
        $keywords = array_filter($words, function($word) {
            return strlen($word) > 4;
        });
        
        // Return top 5 most frequent keywords
        $wordCounts = array_count_values($keywords);
        arsort($wordCounts);
        
        return array_slice(array_keys($wordCounts), 0, 5);
    }
}