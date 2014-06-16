<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->exclude('Tests')
    ->in($dir = 'src');

$versions = GitVersionCollection::create($dir)
    ->addFromTags('v1.0.*')
    ->add('master', 'master branch');

return new Sami($iterator, array(
    'theme' => 'enhanced',
    'versions' => $versions,
    'title' => 'OAuth2 API',
    'build_dir' => __DIR__ . '/../../build/oauth2/%version%',
    'cache_dir' => __DIR__ . '/../../build/cache/oauth2/%version%',
    'include_parent_data' => false,
    'default_opened_level' => 2,
));
