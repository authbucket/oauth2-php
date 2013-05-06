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
 * Define the AuthorizesInterface.
 */
interface AuthorizesInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return Authorizes
   */
  public function setClientId($clientId);

  /**
   * Get clientId
   *
   * @return string
   */
  public function getClientId();

  /**
   * Set username
   *
   * @param string $username
   * @return Authorizes
   */
  public function setUsername($username);

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername();

  /**
   * Set scope
   *
   * @param array $scope
   * @return Authorizes
   */
  public function setScope($scope);

  /**
   * Get scope
   *
   * @return array
   */
  public function getScope();
}
