<?php

namespace IamLab\Core\Console\Table;

use IamLab\Core\Console\Table\Processors\AssociativeArrayProcessor;
use IamLab\Core\Console\Table\Processors\MultiDimensionalArrayProcessor;
use IamLab\Core\Console\Table\Formatters\ConsoleTableFormatter;
use IamLab\Core\Console\Table\Renderers\ConsoleRenderer;

class Table implements TableInterface
{
    private array $processors;

    private array $data = [];

    private ?string $title = null;

    public function __construct(
        private FormatterInterface $formatter = new ConsoleTableFormatter(),
        private RendererInterface $renderer = new ConsoleRenderer()
    ) {
        // Register default processors
        $this->processors = [
            new AssociativeArrayProcessor(),
            new MultiDimensionalArrayProcessor()
        ];
    }

    /**
     * Display a table with the given data
     */
    public function display(array $data, ?string $title = null): void
    {
        $output = $this->generate($data, $title);
        $this->renderer->render($output);
    }

    /**
     * Generate table output as string
     */
    public function generate(array $data, ?string $title = null): string
    {
        if ($data === []) {
            return "Empty table\n";
        }

        // Process the data to get normalized structure
        $processedData = $this->processData($data);
        $headers = $processedData['headers'];
        $rows = $processedData['rows'];

        // Calculate column widths
        $widths = $this->formatter->calculateWidths($headers, $rows);
        $totalWidth = $this->formatter->calculateTotalWidth($widths);

        $output = '';

        // Add title if provided
        if ($title) {
            $output .= $this->formatter->formatTitle($title, $totalWidth) . "\n";
        }

        // Top border
        $output .= $this->formatter->formatBorder($widths) . "\n";

        // Headers
        $headerRow = [];
        foreach ($headers as $header) {
            $headerRow[$header] = $header;
        }

        $output .= $this->formatter->formatRow($headerRow, $headers, $widths) . "\n";

        // Header separator
        $output .= $this->formatter->formatBorder($widths) . "\n";

        // Data rows
        foreach ($rows as $row) {
            $output .= $this->formatter->formatRow($row, $headers, $widths) . "\n";
        }

        // Bottom border
        $output .= $this->formatter->formatBorder($widths) . "\n";

        return $output;
    }

    /**
     * Process data using appropriate processor
     */
    private function processData(array $data): array
    {
        foreach ($this->processors as $processor) {
            if ($processor->canProcess($data)) {
                return $processor->process($data);
            }
        }

        // Fallback: treat as simple associative array
        return [
            'headers' => array_keys($data),
            'rows' => [$data]
        ];
    }

    /**
     * Add a custom data processor
     */
    public function addProcessor(DataProcessorInterface $processor): self
    {
        array_unshift($this->processors, $processor);
        return $this;
    }

    /**
     * Set the formatter
     */
    public function setFormatter(FormatterInterface $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Set the renderer
     */
    public function setRenderer(RendererInterface $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Get the current formatter
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * Get the current renderer
     */
    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }

    /**
     * Check if the table can be rendered in current environment
     */
    public function isSupported(): bool
    {
        return $this->renderer->isSupported();
    }

    /**
     * Set the data to be displayed in the table
     */
    #[\Override]
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the table title
     */
    #[\Override]
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Render and output the table
     */
    #[\Override]
    public function render(): void
    {
        $this->display($this->data, $this->title);
    }

    /**
     * Get the table as a string without outputting
     */
    #[\Override]
    public function toString(): string
    {
        return $this->generate($this->data, $this->title);
    }
}
