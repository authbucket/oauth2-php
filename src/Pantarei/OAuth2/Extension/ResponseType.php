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
 * Defines the abstract class for response type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class ResponseType implements OAuth2TypeInterface
{
  protected $app;

  public function __construct(Application $app)
  {
    $this->app = $app;
  }  

  public function buildType()
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
    return 'response_type';
  }
}
