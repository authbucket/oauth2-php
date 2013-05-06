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
 * Define the RefreshTokensInterface.
 */
interface RefreshTokensInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set refreshToken
   *
   * @param string $refreshToken
   * @return RefreshTokens
   */
  public function setRefreshToken($refreshToken);

  /**
   * Get refreshToken
   *
   * @return string
   */
  public function getRefreshToken();

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return RefreshTokens
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
   * @return RefreshTokens
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
   * @return RefreshTokens
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
   * @return RefreshTokens
   */
  public function setScope($scope);

  /**
   * Get scope
   *
   * @return array
   */
  public function getScope();
}
