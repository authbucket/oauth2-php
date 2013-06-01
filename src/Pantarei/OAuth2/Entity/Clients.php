<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Clients
 *
 * @Table(name="clients")
 * @Entity(repositoryClass="Pantarei\OAuth2\Entity\ClientsRepository")
 */
class Clients implements UserInterface
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
     * @Column(name="client_id", type="string", length=255)
     */
    private $client_id;

    /**
     * @var string
     *
     * @Column(name="client_secret", type="string", length=255)
     */
    private $client_secret;

    /**
     * @var string
     *
     * @Column(name="redirect_uri", type="text")
     */
    private $redirect_uri;

    /**
     * @var string
     *
     * @Column(name="salt", type="string", length=255)
     */
    private $salt;

    public function __construct()
    {
        $this->redirect_uri = '';
        $this->salt = md5(uniqid(null, true));
    }

    public function getRoles()
    {
        return array('ROLE_USER');
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
        return $this->setClientId($username);
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->getClientId();
    }

    /**
     * Set password
     *
     * @param string $password
     * @return Users
     */
    public function setPassword($password)
    {
        return $this->setClientSecret($password);
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->getClientSecret();
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Set client_id
     *
     * @param string $client_id
     * @return Clients
     */
    public function setClientId($client_id)
    {
        $this->client_id = $client_id;

        return $this;
    }

    /**
     * Get client_id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
    }

    /**
     * Set client_secret
     *
     * @param string $client_secret
     * @return Clients
     */
    public function setClientSecret($client_secret)
    {
        $this->client_secret = $client_secret;

        return $this;
    }

    /**
     * Get client_secret
     *
     * @return string
     */
    public function getClientSecret()
    {
        return $this->client_secret;
    }

    /**
     * Set redirect_uri
     *
     * @param string $redirect_uri
     * @return Clients
     */
    public function setRedirectUri($redirect_uri)
    {
        $this->redirect_uri = $redirect_uri;

        return $this;
    }

    /**
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
    }
}
