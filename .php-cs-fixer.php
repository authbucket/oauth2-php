<?php

$fixers = [
    '-no_empty_lines_after_phpdocs',
    '-psr0',
    'ordered_use',
    'php_unit_construct',
    'php_unit_strict',
    'phpdoc_order',
    'short_array_syntax',
];

$finder = Symfony\CS\Finder\DefaultFinder::create()
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

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->setUsingCache(false)
    ->fixers($fixers)
    ->finder($finder);
