<?php

$fixers = array(
    '-psr0',
);

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('app/cache')
    ->exclude('app/log')
    ->exclude('build')
    ->exclude('vendor')
    ->in(__DIR__)
    ->notName('*.phar')
    ->notName('LICENSE')
    ->notName('README.md')
    ->notName('composer.*')
    ->notName('phpunit.xml*');

return Symfony\CS\Config\Config::create()
    ->setUsingCache(false)
    ->fixers($fixers)
    ->finder($finder);
