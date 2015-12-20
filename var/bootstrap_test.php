<?php

passthru(__DIR__.'/../bin/console --env=test -q doctrine:database:drop --force');
passthru(__DIR__.'/../bin/console --env=test -q doctrine:database:create');
passthru(__DIR__.'/../bin/console --env=test -q doctrine:schema:drop');
passthru(__DIR__.'/../bin/console --env=test -q doctrine:schema:create');
passthru(__DIR__.'/../bin/console --env=test -q doctrine:fixtures:load -n');

require __DIR__.'/bootstrap.php';
