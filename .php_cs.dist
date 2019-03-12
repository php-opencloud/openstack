<?php

if (!file_exists(__DIR__.'/src')) {
    exit(0);
}

return PhpCsFixer\Config::create()
    ->setRules(
        [
            '@PSR2'                  => true,
            '@Symfony'               => true,
            'array_syntax'           => ['syntax' => 'short'],
            'binary_operator_spaces' => ['default' => 'align'],
            'protected_to_private'   => false,
        ]
    )
    ->setUsingCache(false)
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__.'/src')
            ->append([__FILE__, __DIR__.'/samples'])
    );
