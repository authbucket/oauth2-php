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
use Pantarei\Oauth2\Entity\AuthorizesInterface;

/**
 * Authorizes
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\AuthorizesRepository")
 */
class Authorizes implements AuthorizesInterface
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
   * Set clientId
   *
   * @param string $clientId
   * @return Authorizes
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
   * Set username
   *
   * @param string $username
   * @return Authorizes
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
   * @return Authorizes
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
