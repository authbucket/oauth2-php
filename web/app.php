<?php

require __DIR__.'/../app/bootstrap.php';

$app = new Silex\Application(array('env' => 'prod'));

require __DIR__.'/../app/AppKernel.php';

$app->run();
