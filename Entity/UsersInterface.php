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
 * Define the UsersInterface.
 */
interface UsersInterface
{
  /**
   * Get id
   *
   * @return integer
   */
  public function getId();

  /**
   * Set username
   *
   * @param string $username
   * @return Users
   */
  public function setUsername($username);

  /**
   * Get username
   *
   * @return string
   */
  public function getUsername();

  /**
   * Set password
   *
   * @param string $password
   * @return Users
   */
  public function setPassword($password);

  /**
   * Get password
   *
   * @return string
   */
  public function getPassword();
}
