<?php
declare(strict_types=1);

namespace IamLab\Seeding\Support;

interface ClockInterface
{
    public function now(): \DateTimeImmutable;
}
