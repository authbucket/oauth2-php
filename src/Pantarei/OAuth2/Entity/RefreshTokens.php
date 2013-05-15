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
 * RefreshTokens
 *
 * @Table(name="refresh_tokens")
 * @Entity(repositoryClass="Pantarei\OAuth2\Entity\RefreshTokensRepository")
 */
class RefreshTokens
{
  /**
   * @var integer
   *
   * @Column(name="id", type="integer")
   * @Id
   * @GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @Column(name="refresh_token", type="string", length=255)
   */
  private $refresh_token;

  /**
   * @var string
   *
   * @Column(name="client_id", type="string", length=255)
   */
  private $client_id;

  /**
   * @var string
   *
   * @Column(name="username", type="string", length=255)
   */
  private $username;

  /**
   * @var integer
   *
   * @Column(name="expires", type="integer")
   */
  private $expires;

  /**
   * @var array
   *
   * @Column(name="scope", type="array")
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
   * Set expires
   *
   * @param integer $expires
   * @return RefreshTokens
   */
  public function setExpires($expires)
  {
    $this->expires = $expires;

    return $this;
  }

  /**
   * Get expires
   *
   * @return integer
   */
  public function getExpires()
  {
    return $this->expires;
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
