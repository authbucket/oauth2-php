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
use Pantarei\Oauth2\Entity\UsersInterface;

/**
 * Users
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\UsersRepository")
 */
class Users implements UsersInterface
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
   * @ORM\Column(name="username", type="string", length=255)
   */
  private $username;

  /**
   * @var string
   *
   * @ORM\Column(name="password", type="string", length=255)
   */
  private $password;

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
   * Set username
   *
   * @param string $username
   * @return Users
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
   * Set password
   *
   * @param string $password
   * @return Users
   */
  public function setPassword($password)
  {
    $this->password = $password;

    return $this;
  }

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword()
  {
    return $this->password;
  }
}
