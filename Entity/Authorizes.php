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

use Pantarei\OAuth2\Entity\EntityInterface;

/**
 * Authorizes
 */
class Authorizes implements EntityInterface
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $client_id;

  /**
   * @var string
   */
  private $username;

  /**
   * @var array
   */
  private $scope;

  /**
   * Set id
   *
   * @param string $id
   * @return Authorizes
   */
  public function setId($id)
  {
    $this->id = $id;

    return $this;
  }

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
   * Set client_id
   *
   * @param string $client_id
   * @return Authorizes
   */
  public function setClientId($client_id)
  {
    $this->client_id = $client_id;

    return $this;
  }

  /**
   * Get client_id
   *
   * @return string
   */
  public function getClientId()
  {
    return $this->client_id;
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
