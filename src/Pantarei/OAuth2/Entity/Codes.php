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

/**
 * Codes
 *
 * @Table(name="codes")
 * @Entity(repositoryClass="Pantarei\OAuth2\Entity\CodesRepository")
 */
class Codes
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
     * @Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @Column(name="client_id", type="string", length=255)
     */
    private $client_id;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=255)
     */
    private $username;

    /**
     * @var string
     *
     * @Column(name="redirect_uri", type="text")
     */
    private $redirect_uri;

    /**
     * @var integer
     *
     * @Column(name="expires", type="integer")
     */
    private $expires;

    /**
     * @var array
     *
     * @Column(name="scope", type="array")
     */
    private $scope;

    public function __construct()
    {
        $this->redirect_uri = '';
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
     * Set code
     *
     * @param string $code
     * @return Codes
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set client_id
     *
     * @param string $client_id
     * @return Codes
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
     * Set redirect_uri
     *
     * @param string $redirect_uri
     * @return Codes
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

    /**
     * Set expires
     *
     * @param integer $expires
     * @return Codes
     */
    public function setExpires($expires)
    {
        $this->expires = $expires;

        return $this;
    }

    /**
     * Get expires
     *
     * @return integer
     */
    public function getExpires()
    {
        return $this->expires;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return Codes
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
     * @return Codes
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
