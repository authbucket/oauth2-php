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
 * Define the ClientsInterface.
 */
interface ClientsInterface
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
   * @return Clients
   */
  public function setClientId($clientId);

  /**
   * Get clientId
   *
   * @return string
   */
  public function getClientId();

  /**
   * Set clientSecret
   *
   * @param string $clientSecret
   * @return Clients
   */
  public function setClientSecret($clientSecret);

  /**
   * Get clientSecret
   *
   * @return string
   */
  public function getClientSecret();

  /**
   * Set redirectUri
   *
   * @param string $redirectUri
   * @return Clients
   */
  public function setRedirectUri($redirectUri);

  /**
   * Get redirectUri
   *
   * @return string
   */
  public function getRedirectUri();
}
