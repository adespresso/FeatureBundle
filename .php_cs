<?php

return PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setUsingCache(false)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        'ordered_imports' => true,
        'phpdoc_order' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->exclude('Resources')
            ->exclude('vendor')
            ->in(__DIR__)
    );
