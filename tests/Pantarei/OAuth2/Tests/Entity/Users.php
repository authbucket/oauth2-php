<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Tests\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Users
 *
 * @Table(name="users")
 * @Entity(repositoryClass="Pantarei\OAuth2\Tests\Entity\UsersRepository")
 */
class Users implements UserInterface
{
    /**
     * @var integer
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @Column(name="salt", type="string", length=255)
     */
    private $salt;

    public function __construct()
    {
        $this->salt = md5(uniqid(null, true));
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
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
}
