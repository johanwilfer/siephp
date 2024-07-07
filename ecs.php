<?php

declare(strict_types=1);

use PhpCsFixer\FixerFactory;
use PhpCsFixer\RuleSet\RuleSet;
use Symplify\EasyCodingStandard\Config\ECSConfig;

$config = ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/examples',
    ])

    // add sets - group of rules
    ->withPreparedSets(
        psr12: true,
        arrays: true,
        comments: true,
        docblocks: true,
        spaces: true,
        namespaces: true,
    )
;

// source: https://hugo.alliau.me/blog/posts/2023-07-19-how-to-use-php-cs-fixer-ruleset-with-easy-coding-standard
// Configure Symfony and Symfony Risky SetList from PHP-CS-Fixer, since they are not shipped anymore with Easy Coding Standard.
$fixerFactory = new FixerFactory();
$fixerFactory->registerBuiltInFixers();
$ruleSet = new RuleSet([
    '@Symfony' => true,
    // You can also enable the risky ruleset if you want.
    '@Symfony:risky' => true,
]);
$fixerFactory->useRuleSet($ruleSet);

foreach ($fixerFactory->getFixers() as $fixer) {
    if (null !== $fixerConfiguration = $ruleSet->getRuleConfiguration($fixer->getName())) {
        $config->withConfiguredRule($fixer::class, $fixerConfiguration);
    } else {
        $config->withRules([$fixer::class]);
    }
}

return $config;