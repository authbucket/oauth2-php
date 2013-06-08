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

use Pantarei\OAuth2\Model\ClientInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Client
 *
 * @Table(name="client")
 * @Entity(repositoryClass="Pantarei\OAuth2\Tests\Entity\ClientRepository")
 */
class Client implements ClientInterface, UserInterface
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set client_id
     *
     * @param string $client_id
     * @return Client
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
     * @return Client
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
     * @return Client
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

    public function __construct()
    {
        $this->redirect_uri = '';
    }

    public function getRoles()
    {
        return array('ROLE_USER');
    }

    public function getPassword()
    {
        return $this->client_secret;
    }

    public function getSalt()
    {
        return '';
    }

    public function getUsername()
    {
        return $this->client_id;
    }

    public function eraseCredentials()
    {
    }


}
