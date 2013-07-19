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

use Pantarei\OAuth2\Model\ScopeInterface;

/**
 * Scope
 *
 * @Table(name="scope")
 * @Entity(repositoryClass="Pantarei\OAuth2\Tests\Entity\ScopeRepository")
 */
class Scope implements ScopeInterface
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
     * @Column(name="scope", type="string", length=255)
     */
    protected $scope;

    public function __construct(
        $scope
    )
    {
        $this->scope = $scope;
    }

    /**
     * Get scope
     *
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }
}
