<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/examples',
    ])

    // add sets - group of rules
    ->withPreparedSets(
        psr12: true,
        // arrays: true,
        // namespaces: true,
        // spaces: true,
        // docblocks: true,
        // comments: true,
    )
;
