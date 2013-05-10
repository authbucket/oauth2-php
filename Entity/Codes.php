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
 * Codes
 */
class Codes implements EntityInterface
{
  /**
   * @var integer
   */
  private $id;

  /**
   * @var string
   */
  private $code;

  /**
   * @var string
   */
  private $client_id;

  /**
   * @var string
   */
  private $username;

  /**
   * @var string
   */
  private $redirect_uri;

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
   * @return Codes
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
   * Set code
   *
   * @param string $code
   * @return Codes
   */
  public function setCode($code)
  {
    $this->code = $code;

    return $this;
  }

  /**
   * Get code
   *
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }

  /**
   * Set client_id
   *
   * @param string $client_id
   * @return Codes
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
   * Set redirect_uri
   *
   * @param string $redirect_uri
   * @return Codes
   */
  public function setRedirectUri($redirect_uri)
  {
    $this->redirect_uri = $redirect_uri;

    return $this;
  }

  /**
   * Get redirect_uri
   *
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirect_uri;
  }

  /**
   * Set expires_in
   *
   * @param integer $expires_in
   * @return Codes
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
   * @return Codes
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
   * @return Codes
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
