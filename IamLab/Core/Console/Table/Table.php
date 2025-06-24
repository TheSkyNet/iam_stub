<?php

namespace IamLab\Core\Console\Table;

use IamLab\Core\Console\Table\Processors\AssociativeArrayProcessor;
use IamLab\Core\Console\Table\Processors\MultiDimensionalArrayProcessor;
use IamLab\Core\Console\Table\Formatters\ConsoleTableFormatter;
use IamLab\Core\Console\Table\Renderers\ConsoleRenderer;

class Table implements TableInterface
{
    private FormatterInterface $formatter;
    private RendererInterface $renderer;
    private array $processors;
    private array $data = [];
    private ?string $title = null;

    public function __construct(
        FormatterInterface $formatter = null,
        RendererInterface $renderer = null
    ) {
        $this->formatter = $formatter ?? new ConsoleTableFormatter();
        $this->renderer = $renderer ?? new ConsoleRenderer();

        // Register default processors
        $this->processors = [
            new AssociativeArrayProcessor(),
            new MultiDimensionalArrayProcessor()
        ];
    }

    /**
     * Display a table with the given data
     *
     * @param array $data
     * @param string|null $title
     * @return void
     */
    public function display(array $data, string $title = null): void
    {
        $output = $this->generate($data, $title);
        $this->renderer->render($output);
    }

    /**
     * Generate table output as string
     *
     * @param array $data
     * @param string|null $title
     * @return string
     */
    public function generate(array $data, string $title = null): string
    {
        if (empty($data)) {
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
     *
     * @param array $data
     * @return array
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
     *
     * @param DataProcessorInterface $processor
     * @return self
     */
    public function addProcessor(DataProcessorInterface $processor): self
    {
        array_unshift($this->processors, $processor);
        return $this;
    }

    /**
     * Set the formatter
     *
     * @param FormatterInterface $formatter
     * @return self
     */
    public function setFormatter(FormatterInterface $formatter): self
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Set the renderer
     *
     * @param RendererInterface $renderer
     * @return self
     */
    public function setRenderer(RendererInterface $renderer): self
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Get the current formatter
     *
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface
    {
        return $this->formatter;
    }

    /**
     * Get the current renderer
     *
     * @return RendererInterface
     */
    public function getRenderer(): RendererInterface
    {
        return $this->renderer;
    }

    /**
     * Check if the table can be rendered in current environment
     *
     * @return bool
     */
    public function isSupported(): bool
    {
        return $this->renderer->isSupported();
    }

    /**
     * Set the data to be displayed in the table
     *
     * @param array $data
     * @return self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set the table title
     *
     * @param string|null $title
     * @return self
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Render and output the table
     *
     * @return void
     */
    public function render(): void
    {
        $this->display($this->data, $this->title);
    }

    /**
     * Get the table as a string without outputting
     *
     * @return string
     */
    public function toString(): string
    {
        return $this->generate($this->data, $this->title);
    }
}
