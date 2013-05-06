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
 * Define the AccessTokensInterface.
 */
interface AccessTokensInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set accessToken
   *
   * @param string $accessToken
   * @return AccessTokens
   */
  public function setAccessToken($accessToken);

  /**
   * Get accessToken
   *
   * @return string
   */
  public function getAccessToken();

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return AccessTokens
   */
  public function setClientId($clientId);

  /**
   * Get clientId
   *
   * @return string
   */
  public function getClientId();

  /**
   * Set expiresIn
   *
   * @param integer $expiresIn
   * @return AccessTokens
   */
  public function setExpiresIn($expiresIn);

  /**
   * Get expiresIn
   *
   * @return integer
   */
  public function getExpiresIn();

  /**
   * Set username
   *
   * @param string $username
   * @return AccessTokens
   */
  public function setUsername($username);

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername();

  /**
   * Set scope
   *
   * @param array $scope
   * @return AccessTokens
   */
  public function setScope($scope);

  /**
   * Get scope
   *
   * @return array
   */
  public function getScope();
}
