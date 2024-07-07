<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
    ])

    ->withPhpSets(php81: true)
    ->withPreparedSets(
        typeDeclarations: true,
    )

    // DTOs looks a bit ugly with this, lets consider if we want this
    ->withSkip([
        \Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
    ])
;
