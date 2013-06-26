<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PantaRei\OAuth2\Tests\Model;

use PantaRei\OAuth2\Model\AbstractAccessToken;

/**
 * AccessToken
 *
 * @Table(name="access_token")
 * @Entity(repositoryClass="PantaRei\OAuth2\Tests\Model\AccessTokenManager")
 */
class AccessToken extends AbstractAccessToken
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
     * @Column(name="access_token", type="string", length=255)
     */
    protected $access_token;

    /**
     * @var string
     *
     * @Column(name="token_type", type="string", length=255)
     */
    protected $token_type;

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

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
}
