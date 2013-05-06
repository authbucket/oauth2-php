<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\Tests\Entity;

use Doctrine\ORM\Mapping as ORM;
use Pantarei\Oauth2\Entity\ScopesInterface;

/**
 * Scopes
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Pantarei\Oauth2\Tests\Entity\ScopesRepository")
 */
class Scopes implements ScopesInterface
{
  /**
   * @var integer
   *
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  private $id;

  /**
   * @var string
   *
   * @ORM\Column(name="scope", type="string", length=255)
   */
  private $scope;

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
   * Set scope
   *
   * @param string $scope
   * @return Scopes
   */
  public function setScope($scope)
  {
    $this->scope = $scope;

    return $this;
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
