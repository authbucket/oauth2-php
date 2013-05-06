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
use Pantarei\Oauth2\Entity\ClientsInterface;

/**
 * Clients
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\ClientsRepository")
 */
class Clients implements ClientsInterface
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
   * @ORM\Column(name="client_secret", type="string", length=255)
   */
  private $clientSecret;

  /**
   * @var string
   *
   * @ORM\Column(name="redirect_uri", type="text")
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
