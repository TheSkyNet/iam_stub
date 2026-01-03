<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\Set\ValueObject\DeadCodeSetList;
use Rector\CodingStyle\Rector\Namespace_\ImportFullyQualifiedNamesRector;

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

    // Target modern PHP features up to 8.4 and safe quality improvements
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_84,
        SetList::CODE_QUALITY,
        SetList::TYPE_DECLARATION,
        SetList::EARLY_RETURN,
        DeadCodeSetList::DEAD_CODE,
        SetList::CODING_STYLE,
    ]);

    // Prefer explicit imports over fully qualified names in code
    $rectorConfig->rules([
        ImportFullyQualifiedNamesRector::class,
    ]);

    // Import class/function/const names into namespaces
    $rectorConfig->importNames();
    // Keep short class imports disabled to avoid confusion with similarly named classes
    $rectorConfig->importShortClasses(false);

    // Enable parallel processing and default cache
    $rectorConfig->parallel();
};
