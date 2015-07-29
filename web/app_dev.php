<?php

require __DIR__.'/../app/bootstrap.php';

$app = new Silex\Application(array('env' => 'dev'));

require __DIR__.'/../app/AppKernel.php';

$app->run();
