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
 * Define the CodesInterface.
 */
interface CodesInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set code
   *
   * @param string $code
   * @return Codes
   */
  public function setCode($code);

  /**
   * Get code
   *
   * @return string
   */
  public function getCode();

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return Codes
   */
  public function setClientId($clientId);

  /**
   * Get clientId
   *
   * @return string
   */
  public function getClientId();

  /**
   * Set redirectUri
   *
   * @param string $redirectUri
   * @return Codes
   */
  public function setRedirectUri($redirectUri);

  /**
   * Get redirectUri
   *
   * @return string
   */
  public function getRedirectUri();

  /**
   * Set expiresIn
   *
   * @param integer $expiresIn
   * @return Codes
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
   * @return Codes
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
   * @return Codes
   */
  public function setScope($scope);

  /**
   * Get scope
   *
   * @return array
   */
  public function getScope();
}
