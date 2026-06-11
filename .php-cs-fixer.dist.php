<?php

$finder = (new PhpCsFixer\Finder())
    ->in([__DIR__ . '/src', __DIR__ . '/tests', __DIR__ . '/public', __DIR__ . '/config'])
    ->exclude('var');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(false)
    ->setRules([
        '@PSR12' => true,

        'concat_space' => ['spacing' => 'one'],
        'not_operator_with_space' => true,
        'not_operator_with_successor_space' => false,
        'no_spaces_inside_parenthesis' => true,
        'unary_operator_spaces' => false,
        'no_extra_blank_lines' => ['tokens' => ['extra', 'curly_brace_block', 'parenthesis_brace_block', 'square_brace_block', 'return', 'throw', 'use', 'use_trait']],

        'fully_qualified_strict_types' => false,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'blank_line_before_statement' => [
            'statements' => ['return'],
        ],
    ])
    ->setFinder($finder);
