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
            return match ($dataType) {
                'csv' => $this->processCsvData($data, $options),
                'json' => $this->processJsonData($data, $options),
                'image' => $this->processImageData($data, $options),
                'text' => $this->processTextData($data, $options),
                default => 'Unsupported data type: ' . $dataType,
            };
        } catch (Exception $exception) {
            return 'Failed to process data: ' . $exception->getMessage();
        }
    }

    /**
     * Process CSV data
     */
    protected function processCsvData(mixed $data, array $options): bool|string
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

            error_log(sprintf('Processed %d CSV rows', $processedCount));
            return true;
        } catch (Exception $exception) {
            return "CSV processing failed: " . $exception->getMessage();
        }
    }

    /**
     * Process JSON data
     */
    protected function processJsonData(mixed $data, array $options): bool|string
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

            error_log(sprintf('Processed JSON data with %d items', $itemCount));
            return true;
        } catch (Exception $exception) {
            return "JSON processing failed: " . $exception->getMessage();
        }
    }

    /**
     * Process image data
     */
    protected function processImageData(mixed $data, array $options): bool|string
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

            error_log(sprintf('Processed image: %s with operations: ', $imagePath) . implode(', ', $operations));
            return true;
        } catch (Exception $exception) {
            return "Image processing failed: " . $exception->getMessage();
        }
    }

    /**
     * Process text data
     */
    protected function processTextData(mixed $data, array $options): bool|string
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
        } catch (Exception $exception) {
            return "Text processing failed: " . $exception->getMessage();
        }
    }

    /**
     * Process a single row (helper method)
     */
    protected function processRow(mixed $row): void
    {
        // Simulate row processing logic
        // In a real implementation, this might involve database operations,
        // data validation, transformation, etc.
    }

    /**
     * Simulate image resize operation
     */
    protected function simulateImageResize(string $imagePath, array $options): void
    {
        $width = $options['width'] ?? 800;
        $height = $options['height'] ?? 600;
        error_log(sprintf('Simulating image resize: %s to %sx%s', $imagePath, $width, $height));
    }

    /**
     * Simulate image compress operation
     */
    protected function simulateImageCompress(string $imagePath, array $options): void
    {
        $quality = $options['quality'] ?? 80;
        error_log(sprintf('Simulating image compression: %s at %s%% quality', $imagePath, $quality));
    }

    /**
     * Simulate image watermark operation
     */
    protected function simulateImageWatermark(string $imagePath, array $options): void
    {
        $watermark = $options['watermark'] ?? 'default.png';
        error_log(sprintf('Simulating watermark application: %s with %s', $imagePath, $watermark));
    }

    /**
     * Simulate sentiment analysis
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
        }

        if ($negativeCount > $positiveCount) {
            return 'negative';
        }

        return 'neutral';
    }

    /**
     * Simulate keyword extraction
     */
    protected function simulateKeywordExtraction(string $text): array
    {
        // Simple simulation - extract words longer than 4 characters
        $words = str_word_count(strtolower($text), 1);
        $keywords = array_filter($words, fn($word): bool => strlen($word) > 4);

        // Return top 5 most frequent keywords
        $wordCounts = array_count_values($keywords);
        arsort($wordCounts);

        return array_slice(array_keys($wordCounts), 0, 5);
    }
}
