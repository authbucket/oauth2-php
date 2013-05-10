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
 * AccessTokens
 */
class AccessTokens implements EntityInterface
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $access_token;

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
   * @return AccessTokens
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
   * Set access_token
   *
   * @param string $access_token
   * @return AccessTokens
   */
  public function setAccessToken($access_token)
  {
    $this->access_token = $access_token;

    return $this;
  }

  /**
   * Get access_token
   *
   * @return string
   */
  public function getAccessToken()
  {
    return $this->access_token;
  }

  /**
   * Set client_id
   *
   * @param string $client_id
   * @return AccessTokens
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
   * @return AccessTokens
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
   * @return AccessTokens
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
   * @return AccessTokens
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
