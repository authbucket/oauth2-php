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
  private $client_id;

  /**
   * @var string
   */
  private $client_secret;

  /**
   * @var string
   */
  private $redirect_uri;

  /**
   * Set id
   *
   * @param string $id
   * @return Clients
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
   * @return Clients
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
   * Set client_secret
   *
   * @param string $client_secret
   * @return Clients
   */
  public function setClientSecret($client_secret)
  {
    $this->client_secret = $client_secret;

    return $this;
  }

  /**
   * Get client_secret
   *
   * @return string
   */
  public function getClientSecret()
  {
    return $this->client_secret;
  }

  /**
   * Set redirect_uri
   *
   * @param string $redirect_uri
   * @return Clients
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
}
