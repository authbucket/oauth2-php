<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Entity;

use Pantarei\Oauth2\Entity\EntityManagerInterface;

/**
 * Define the EntityReposityInterface.
 */
class EntityManager extends \Doctrine\ORM\EntityManager implements EntityManagerInterface
{
}
