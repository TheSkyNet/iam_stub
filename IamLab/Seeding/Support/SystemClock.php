<?php

declare(strict_types=1);

namespace IamLab\Seeding\Support;

final class SystemClock implements ClockInterface
{
    #[\Override]
    public function now(): \DateTimeImmutable
    {
        return new \DateTimeImmutable('now');
    }
}
