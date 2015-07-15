<?php

passthru(__DIR__.'/console --env=test -q doctrine:database:drop --force');
passthru(__DIR__.'/console --env=test -q doctrine:database:create');
passthru(__DIR__.'/console --env=test -q doctrine:schema:drop --force');
passthru(__DIR__.'/console --env=test -q doctrine:schema:create');
passthru(__DIR__.'/console --env=test -q doctrine:fixtures:load -n');

$loader = require __DIR__.'/bootstrap.php';
$loader->add('AuthBucket\OAuth2\Tests', __DIR__.'../tests');
