<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension;

use Pantarei\OAuth2\OAuth2TypeInterface;
use Silex\Application;

/**
 * Defines the abstract class for grant type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class GrantType implements OAuth2TypeInterface
{
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }

  public function buildType($query, $filtered_query)
  {
    return TRUE;
  }

  public function buildView()
  {
    return TRUE;
  }

  public function finishView()
  {
    return TRUE;
  }

  public function getParent()
  {
    return NULL;
  }

  public function getName()
  {
    return 'grant_type';
  }

}
