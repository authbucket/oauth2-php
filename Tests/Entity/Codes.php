<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Entity;

/**
 * Codes
 *
 * @Table()
 * @Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\CodesRepository")
 */
class Codes extends \Pantarei\Oauth2\Entity\Codes
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
   * @Column(name="code", type="string", length=255)
   */
  private $code;

  /**
   * @var string
   *
   * @Column(name="client_id", type="string", length=255)
   */
  private $clientId;

  /**
   * @var string
   *
   * @Column(name="username", type="string", length=255)
   */
  private $username;

  /**
   * @var string
   *
   * @Column(name="redirect_uri", type="text")
   */
  private $redirectUri;

  /**
   * @var integer
   *
   * @Column(name="expires_in", type="integer")
   */
  private $expiresIn;

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
   * Set clientId
   *
   * @param string $clientId
   * @return Codes
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
   * Set redirectUri
   *
   * @param string $redirectUri
   * @return Codes
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;

    return $this;
  }

  /**
   * Get redirectUri
   *
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }

  /**
   * Set expiresIn
   *
   * @param integer $expiresIn
   * @return Codes
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;

    return $this;
  }

  /**
   * Get expiresIn
   *
   * @return integer
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
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
