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
 * Authorizes
 */
class Authorizes
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $clientId;

  /**
   * @var string
   */
  private $username;

  /**
   * @var array
   */
  private $scope;

  /**
   * Get id
   *
   * @return integer
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return Authorizes
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;

    return $this;
  }

  /**
   * Get clientId
   *
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }

  /**
   * Set username
   *
   * @param string $username
   * @return Authorizes
   */
  public function setUsername($username)
  {
    $this->username = $username;

    return $this;
  }

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }

  /**
   * Set scope
   *
   * @param array $scope
   * @return Authorizes
   */
  public function setScope($scope)
  {
    $this->scope = $scope;

    return $this;
  }

  /**
   * Get scope
   *
   * @return array
   */
  public function getScope()
  {
    return $this->scope;
  }
}
