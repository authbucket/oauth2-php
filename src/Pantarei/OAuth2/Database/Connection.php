<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Database;

/**
 * Base database API class.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
abstract class Connection
{
  abstract public function find($entityName, $id);

  abstract public function findBy($entityName, $criteria);

  abstract public function findOneBy($entityName, $criteria);

  abstract public function findAll($entityName);

  abstract public function persist($entity);

  abstract public function remove($entity);
}
