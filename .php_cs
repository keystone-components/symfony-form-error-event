<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests');

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@Symfony' => true,
        'ordered_imports' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'array_syntax' => ['syntax' => 'short'],
        'phpdoc_align' => false,
    ]);
