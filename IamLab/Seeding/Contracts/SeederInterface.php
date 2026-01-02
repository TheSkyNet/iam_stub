<?php
declare(strict_types=1);

namespace IamLab\Seeding\Contracts;

interface SeederInterface
{
    public function run(): void;
}
