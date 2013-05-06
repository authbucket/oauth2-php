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

use Doctrine\ORM\Mapping as ORM;
use Pantarei\Oauth2\Entity\AccessTokensInterface;

/**
 * AccessTokens
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\AccessTokensRepository")
 */
class AccessTokens implements AccessTokensInterface
{
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="access_token", type="string", length=255)
   */
  private $accessToken;

  /**
   * @var string
   *
   * @ORM\Column(name="client_id", type="string", length=255)
   */
  private $clientId;

  /**
   * @var string
   *
   * @ORM\Column(name="username", type="string", length=255)
   */
  private $username;

  /**
   * @var integer
   *
   * @ORM\Column(name="expires_in", type="integer")
   */
  private $expiresIn;

  /**
   * @var array
   *
   * @ORM\Column(name="scope", type="array")
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
   * Set accessToken
   *
   * @param string $accessToken
   * @return AccessTokens
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;

    return $this;
  }

  /**
   * Get accessToken
   *
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }

  /**
   * Set clientId
   *
   * @param string $clientId
   * @return AccessTokens
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
   * Set expiresIn
   *
   * @param integer $expiresIn
   * @return AccessTokens
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
