<?php
declare(strict_types=1);

namespace IamLab\Seeding\Registry;

use IamLab\Seeding\Contracts\BuildsFromOptions;

final class SeederRegistry
{
    /** @param array<string,class-string> $mapping */
    public function __construct(private array $mapping)
    {
    }

    /** @return array<int,string> */
    public function collectCliOptions(): array
    {
        $opts = [];
        foreach ($this->mapping as $class) {
            if (is_string($class) && class_exists($class) && is_callable([$class, 'cliOptions'])) {
                $list = (array) $class::cliOptions();
                foreach ($list as $opt) {
                    if (is_string($opt) && $opt !== '') {
                        $opts[$opt] = true;
                    }
                }
            }
        }
        return array_keys($opts);
    }

    /** @param array<string,mixed> $options */
    public function instantiate(string $class, array $options): object
    {
        if (is_callable([$class, 'fromOptions'])) {
            /** @var class-string<BuildsFromOptions> $class */
            return $class::fromOptions($options);
        }
        return new $class();
    }
}
