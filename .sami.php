<?php

use Sami\Sami;
use Sami\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('Resources')
    ->in($dir = 'src');

$versions = GitVersionCollection::create($dir)
    ->add('develop', 'master branch')
    ->add('master', 'master branch')
    ->addFromTags('*');

return new Sami($iterator, array(
    'theme' => 'enhanced',
    'versions' => $versions,
    'title' => 'AuthBucket\OAuth2 API',
    'build_dir' => __DIR__ . '/build/oauth2/%version%',
    'cache_dir' => __DIR__ . '/build/cache/oauth2/%version%',
    'include_parent_data' => false,
    'default_opened_level' => 2,
));
