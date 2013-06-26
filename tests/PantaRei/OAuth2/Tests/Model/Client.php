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

use PantaRei\OAuth2\Model\AbstractClient;

/**
 * Client
 *
 * @Table(name="client")
 * @Entity(repositoryClass="PantaRei\OAuth2\Tests\Model\ClientManager")
 */
class Client extends AbstractClient
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
     * @Column(name="client_secret", type="string", length=255)
     */
    protected $client_secret;

    /**
     * @var string
     *
     * @Column(name="redirect_uri", type="blob")
     */
    protected $redirect_uri;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->redirect_uri = '';
    }
}
