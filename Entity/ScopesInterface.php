<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Entity;

/**
 * Define the ScopesInterface
 */
interface ScopesInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set scope
   *
   * @param string $scope
   * @return Scopes
   */
  public function setScope($scope);

  /**
   * Get scope
   *
   * @return string
   */
  public function getScope();
}
