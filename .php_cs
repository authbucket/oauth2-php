<?php

$rules = [
    '@Symfony' => true,
    'array_syntax' => ['syntax' => 'short'],
    'no_blank_lines_after_phpdoc' => false,
    'ordered_class_elements' => true,
    'phpdoc_order' => true,
];

$finder = PhpCsFixer\Finder::create()
    ->exclude('build')
    ->exclude('var/cache')
    ->exclude('var/log')
    ->exclude('vendor')
    ->ignoreDotFiles(false)
    ->ignoreVCS(true)
    ->in(__DIR__)
    ->notName('*.phar')
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('composer.*')
    ->notName('phpunit.xml*');

return PhpCsFixer\Config::create()
    ->setUsingCache(false)
    ->setRules($rules)
    ->setFinder($finder);
