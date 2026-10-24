<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    // Limit Rector to application and tests
    $rectorConfig->paths([
        __DIR__ . '/IamLab',
        __DIR__ . '/tests',
    ]);

    // Skip vendor/build and non-PHP folders
    $rectorConfig->skip([
        __DIR__ . '/vendor/*',
        __DIR__ . '/node_modules/*',
        __DIR__ . '/public/*',
        __DIR__ . '/assets/*',
        __DIR__ . '/docker/*',
        __DIR__ . '/files/*',
        __DIR__ . '/junk/*',
        __DIR__ . '/service-templates/*',
        __DIR__ . '/docs/*',
        __DIR__ . '/_docs/*',
    ]);

    // Target the authoritative PHP runtime.
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_85,
    ]);

    // Import class/function/const names into namespaces
    $rectorConfig->importNames();
    // Keep short class imports disabled to avoid confusion with similarly named classes
    $rectorConfig->importShortClasses(false);

    // Enable parallel processing and default cache
    $rectorConfig->parallel();
};
