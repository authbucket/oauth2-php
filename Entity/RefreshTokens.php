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
 * RefreshTokens
 */
class RefreshTokens implements EntityInterface
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $refresh_token;

  /**
   * @var string
   */
  private $client_id;

  /**
   * @var string
   */
  private $username;

  /**
   * @var integer
   */
  private $expires_in;

  /**
   * @var array
   */
  private $scope;

  /**
   * Set id
   *
   * @param string $id
   * @return RefreshTokens
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
   * Set refresh_token
   *
   * @param string $refresh_token
   * @return RefreshTokens
   */
  public function setRefreshToken($refresh_token)
  {
    $this->refresh_token = $refresh_token;

    return $this;
  }

  /**
   * Get refresh_token
   *
   * @return string
   */
  public function getRefreshToken()
  {
    return $this->refresh_token;
  }

  /**
   * Set client_id
   *
   * @param string $client_id
   * @return RefreshTokens
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
   * Set expires_in
   *
   * @param integer $expires_in
   * @return RefreshTokens
   */
  public function setExpiresIn($expires_in)
  {
    $this->expires_in = $expires_in;

    return $this;
  }

  /**
   * Get expires_in
   *
   * @return integer
   */
  public function getExpiresIn()
  {
    return $this->expires_in;
  }

  /**
   * Set username
   *
   * @param string $username
   * @return RefreshTokens
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
   * @return RefreshTokens
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
