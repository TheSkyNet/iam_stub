<?php
declare(strict_types=1);

namespace IamLab\Seeding\Support;

final class ConsolePrinter
{
    public function __construct(private bool $verbose = false)
    {
    }

    public function info(string $msg): void { fwrite(STDOUT, $msg . "\n"); }
    public function warn(string $msg): void { fwrite(STDOUT, "[WARN] " . $msg . "\n"); }
    public function error(string $msg): void { fwrite(STDERR, "[ERROR] " . $msg . "\n"); }
    public function debug(string $msg): void { if ($this->verbose) { fwrite(STDOUT, "[DEBUG] " . $msg . "\n"); } }
}
