<?php

$config = Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'ordered_use',
        'phpdoc_order',
        'short_array_syntax',
    ]);

if (null === $input->getArgument('path')) {
    $config
        ->finder(
            Symfony\CS\Finder\DefaultFinder::create()
                ->exclude('Resources')
                ->in(__DIR__)
        );
}

return $config;
