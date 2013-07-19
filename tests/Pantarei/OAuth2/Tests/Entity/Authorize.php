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

use Pantarei\OAuth2\Model\AuthorizeInterface;

/**
 * Authorize
 *
 * @Table(name="authorize")
 * @Entity(repositoryClass="Pantarei\OAuth2\Tests\Entity\AuthorizeRepository")
 */
class Authorize implements AuthorizeInterface
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
     * @var array
     *
     * @Column(name="scope", type="array")
     */
    protected $scope;

    public function __construct(
        $client_id,
        $username,
        $scope = array()
    )
    {
        $this->client_id = $client_id;
        $this->username = $username;
        $this->scope = $scope;
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
     * Get scope
     *
     * @return array
     */
    public function getScope()
    {
        return $this->scope;
    }
}
