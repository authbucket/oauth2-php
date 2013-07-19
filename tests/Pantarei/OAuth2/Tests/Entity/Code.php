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

use Pantarei\OAuth2\Model\CodeInterface;

/**
 * Code
 *
 * @Table(name="code")
 * @Entity(repositoryClass="Pantarei\OAuth2\Tests\Entity\CodeRepository")
 */
class Code implements CodeInterface
{
    /**
     * @var integer
     *
     * @Column(name="id", type="integer")
     * @Id
     * @GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @Column(name="code", type="string", length=255)
     */
    protected $code;

    /**
     * @var string
     *
     * @Column(name="client_id", type="string", length=255)
     */
    protected $client_id;

    /**
     * @var string
     *
     * @Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string
     *
     * @Column(name="redirect_uri", type="text")
     */
    protected $redirect_uri;

    /**
     * @var \DateTime
     *
     * @Column(name="expires", type="datetime")
     */
    protected $expires;

    /**
     * @var array
     *
     * @Column(name="scope", type="array")
     */
    protected $scope;

    public function __construct(
        $code,
        $client_id,
        $username,
        $redirect_uri,
        $expires,
        $scope = array()
    )
    {
        $this->code = $code;
        $this->client_id = $client_id;
        $this->username = $username;
        $this->redirect_uri = $redirect_uri;
        $this->expires = $expires;
        $this->scope = $scope;
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
     * Get client_id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client_id;
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
     * Get redirect_uri
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->redirect_uri;
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
     * Get scope
     *
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }
}
