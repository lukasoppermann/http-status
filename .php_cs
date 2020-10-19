<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
       ->setRules([
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'concat_space' => ['spacing' => 'none'],
        // 'ordered_use' => true,
        // 'unused_use' => true,
        // 'remove_lines_between_uses' => true,
        // 'remove_leading_slash_use' => true,
        // 'single_array_no_trailing_comma' => true,
        // 'multiline_array_trailing_comma' => true,
        // 'single_quote' => true,
        // 'ternary_spaces' => true,
        // 'operators_spaces' => true,
        // 'new_with_braces' => true,
    ])
    ->setFinder($finder);
