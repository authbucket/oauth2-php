<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Entity;

/**
 * Defines the interface for entity.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface EntityInterface
{
  /**
   * Set id
   *
   * @param string $id
   * @return $this
   */
  public function setId($id);

  /**
   * Get id
   *
   * @return integer
   */
  public function getId();
}
