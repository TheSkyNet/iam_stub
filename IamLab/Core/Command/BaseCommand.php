<?php

namespace IamLab\Core\Command;

use Phalcon\Di\Injectable;

abstract class BaseCommand extends Injectable implements CommandInterface
{
    protected array $arguments = [];
    protected array $options = [];
    protected bool $verbose = false;
    protected bool $debug = false;

    /**
     * Execute the command
     *
     * @param array $arguments Command arguments
     * @param array $options Command options/flags
     * @return int Exit code (0 for success, non-zero for failure)
     */
    public function execute(array $arguments = [], array $options = []): int
    {
        $this->arguments = $arguments;
        $this->options = $options;
        $this->verbose = isset($options['v']) || isset($options['verbose']);
        $this->debug = isset($options['d']) || isset($options['debug']);

        try {
            return $this->handle();
        } catch (\Exception $e) {
            $this->error("Command failed: " . $e->getMessage());
            if ($this->debug) {
                $this->error("Stack trace:");
                $this->error($e->getTraceAsString());
            }
            return 1;
        }
    }

    /**
     * Handle the command execution
     * This method should be implemented by concrete command classes
     *
     * @return int Exit code
     */
    abstract protected function handle(): int;

    /**
     * Get command signature/usage
     * Override this method to provide custom signature
     *
     * @return string
     */
    public function getSignature(): string
    {
        return 'command';
    }

    /**
     * Get command description
     * Override this method to provide custom description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return 'A command';
    }

    /**
     * Get command help text
     * Override this method to provide custom help
     *
     * @return string
     */
    public function getHelp(): string
    {
        return $this->getDescription();
    }

    /**
     * Get argument by index
     *
     * @param int $index
     * @param mixed $default
     * @return mixed
     */
    protected function argument(int $index, $default = null)
    {
        return $this->arguments[$index] ?? $default;
    }

    /**
     * Get option by name
     *
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    protected function option(string $name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Check if option exists
     *
     * @param string $name
     * @return bool
     */
    protected function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Output information message
     *
     * @param string $message
     */
    protected function info(string $message): void
    {
        echo "\033[32m[INFO]\033[0m " . $message . "\n";
    }

    /**
     * Output warning message
     *
     * @param string $message
     */
    protected function warn(string $message): void
    {
        echo "\033[33m[WARN]\033[0m " . $message . "\n";
    }

    /**
     * Output error message
     *
     * @param string $message
     */
    protected function error(string $message): void
    {
        echo "\033[31m[ERROR]\033[0m " . $message . "\n";
    }

    /**
     * Output success message
     *
     * @param string $message
     */
    protected function success(string $message): void
    {
        echo "\033[32m[SUCCESS]\033[0m " . $message . "\n";
    }

    /**
     * Output debug message (only if debug mode is enabled)
     *
     * @param string $message
     */
    protected function debug(string $message): void
    {
        if ($this->debug) {
            echo "\033[36m[DEBUG]\033[0m " . $message . "\n";
        }
    }

    /**
     * Output verbose message (only if verbose mode is enabled)
     *
     * @param string $message
     */
    protected function verbose(string $message): void
    {
        if ($this->verbose) {
            echo "\033[37m[VERBOSE]\033[0m " . $message . "\n";
        }
    }

    /**
     * Output plain message
     *
     * @param string $message
     */
    protected function line(string $message): void
    {
        echo $message . "\n";
    }

    /**
     * Ask user for input
     *
     * @param string $question
     * @param string $default
     * @return string
     */
    protected function ask(string $question, string $default = ''): string
    {
        echo $question;
        if ($default) {
            echo " [default: {$default}]";
        }
        echo ": ";
        
        $input = trim(fgets(STDIN));
        return $input ?: $default;
    }

    /**
     * Ask user for confirmation
     *
     * @param string $question
     * @param bool $default
     * @return bool
     */
    protected function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        echo "{$question} [{$defaultText}]: ";
        
        $input = trim(strtolower(fgets(STDIN)));
        
        if ($input === '') {
            return $default;
        }
        
        return in_array($input, ['y', 'yes', '1', 'true']);
    }

    /**
     * Create a progress bar
     *
     * @param int $total
     * @return ProgressBar
     */
    protected function progressBar(int $total): ProgressBar
    {
        return new ProgressBar($total);
    }
}

/**
 * Simple progress bar implementation
 */
class ProgressBar
{
    private int $total;
    private int $current = 0;
    private int $width = 50;

    public function __construct(int $total)
    {
        $this->total = $total;
    }

    public function advance(int $step = 1): void
    {
        $this->current += $step;
        $this->display();
    }

    public function finish(): void
    {
        $this->current = $this->total;
        $this->display();
        echo "\n";
    }

    private function display(): void
    {
        $percent = $this->total > 0 ? ($this->current / $this->total) * 100 : 100;
        $filled = (int)(($this->current / $this->total) * $this->width);
        $empty = $this->width - $filled;

        $bar = str_repeat('=', $filled) . str_repeat('-', $empty);
        printf("\r[%s] %d%% (%d/%d)", $bar, $percent, $this->current, $this->total);
    }
}