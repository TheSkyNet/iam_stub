<?php

namespace IamLab\Core\Command;

use Exception;
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
    #[\Override]
    public function execute(array $arguments = [], array $options = []): int
    {
        $this->arguments = $arguments;
        $this->options = $options;
        $this->verbose = isset($options['v']) || isset($options['verbose']);
        $this->debug = isset($options['d']) || isset($options['debug']);

        try {
            return $this->handle();
        } catch (Exception $exception) {
            $this->error("Command failed: " . $exception->getMessage());
            if ($this->debug) {
                $this->error("Stack trace:");
                $this->error($exception->getTraceAsString());
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
     */
    #[\Override]
    public function getSignature(): string
    {
        return 'command';
    }

    /**
     * Get command description
     * Override this method to provide custom description
     */
    #[\Override]
    public function getDescription(): string
    {
        return 'A command';
    }

    /**
     * Get command help text
     * Override this method to provide custom help
     */
    #[\Override]
    public function getHelp(): string
    {
        return $this->getDescription();
    }

    /**
     * Get argument by index
     *
     * @return mixed
     */
    protected function argument(int $index, mixed $default = null)
    {
        return $this->arguments[$index] ?? $default;
    }

    /**
     * Get option by name
     *
     * @return mixed
     */
    protected function option(string $name, mixed $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Check if option exists
     */
    protected function hasOption(string $name): bool
    {
        return isset($this->options[$name]);
    }

    /**
     * Output information message
     */
    protected function info(string $message): void
    {
        echo "\033[32m[INFO]\033[0m " . $message . "\n";
    }

    /**
     * Output warning message
     */
    protected function warn(string $message): void
    {
        echo "\033[33m[WARN]\033[0m " . $message . "\n";
    }

    /**
     * Output error message
     */
    protected function error(string $message): void
    {
        echo "\033[31m[ERROR]\033[0m " . $message . "\n";
    }

    /**
     * Output success message
     */
    protected function success(string $message): void
    {
        echo "\033[32m[SUCCESS]\033[0m " . $message . "\n";
    }

    /**
     * Output debug message (only if debug mode is enabled)
     */
    protected function debug(string $message): void
    {
        if ($this->debug) {
            echo "\033[36m[DEBUG]\033[0m " . $message . "\n";
        }
    }

    /**
     * Output verbose message (only if verbose mode is enabled)
     */
    protected function verbose(string $message): void
    {
        if ($this->verbose) {
            echo "\033[37m[VERBOSE]\033[0m " . $message . "\n";
        }
    }

    /**
     * Output plain message
     */
    protected function line(string $message): void
    {
        echo $message . "\n";
    }

    /**
     * Ask user for input
     */
    protected function ask(string $question, string $default = ''): string
    {
        echo $question;
        if ($default !== '' && $default !== '0') {
            echo sprintf(' [default: %s]', $default);
        }

        echo ": ";

        $input = trim(fgets(STDIN));
        return $input ?: $default;
    }

    /**
     * Ask user for confirmation
     */
    protected function confirm(string $question, bool $default = false): bool
    {
        $defaultText = $default ? 'Y/n' : 'y/N';
        echo sprintf('%s [%s]: ', $question, $defaultText);

        $input = trim(strtolower(fgets(STDIN)));

        if ($input === '') {
            return $default;
        }

        return in_array($input, ['y', 'yes', '1', 'true']);
    }

    /**
     * Create a progress bar
     */
    protected function progressBar(int $total): ProgressBar
    {
        return new ProgressBar($total);
    }
}
