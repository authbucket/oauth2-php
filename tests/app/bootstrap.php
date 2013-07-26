<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__ . '/../../vendor/autoload.php';

$loader->add('Pantarei\OAuth2\Tests', __DIR__ . '/../');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
