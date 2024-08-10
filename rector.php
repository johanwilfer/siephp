<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php53\Rector\Ternary\TernaryToElvisRector;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;

//
// https://github.com/rectorphp/rector
// https://getrector.com/documentation/integration-to-new-project
// https://getrector.com/blog
//
// Another good tool from the rector team is installed to be able to use for manual refactors: https://github.com/rectorphp/swiss-knife
//
return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/examples',
        __DIR__ . '/src',
    ])
    ->withPhpSets(php82: true)
    ->withImportNames(importShortClasses: false)
    ->withAttributesSets(all: true)
    // https://getrector.com/documentation/set-lists
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        codingStyle: true,
        typeDeclarations: true,
        privatization: true,
        instanceOf: true,
        strictBooleans: true,
        rectorPreset: true,
        phpunitCodeQuality: true,
        phpunit: true,
    )

    // DTOs looks a bit ugly with this, lets consider if we want this
    ->withSkip([
        ClassPropertyAssignToConstructorPromotionRector::class,
        // this is conflicting with our phpstan rules - either they should change or this needs to be skipped
        TernaryToElvisRector::class,
    ])
;
