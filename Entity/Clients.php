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
 * Clients
 */
class Clients implements EntityInterface
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
  private $clientSecret;

  /**
   * @var string
   */
  private $redirectUri;

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
   * @return Clients
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
   * Set clientSecret
   *
   * @param string $clientSecret
   * @return Clients
   */
  public function setClientSecret($clientSecret)
  {
    $this->clientSecret = $clientSecret;

    return $this;
  }

  /**
   * Get clientSecret
   *
   * @return string
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }

  /**
   * Set redirectUri
   *
   * @param string $redirectUri
   * @return Clients
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
}
