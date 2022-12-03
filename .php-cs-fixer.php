<?php

declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->ignoreDotFiles(false)
    ->ignoreVCSIgnored(true)
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        '@PhpCsFixer:risky' => true,
        'general_phpdoc_annotation_remove' => ['annotations' => ['expectedDeprecation']],
        'heredoc_indentation' => false,
        'modernize_strpos' => true,
        'no_useless_concat_operator' => false,
        'use_arrow_functions' => false,
    ])
    ->setFinder($finder)
;

return $config;