<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\TokenType;

use Pantarei\OAuth2\Extension\TokenType;
use Silex\Application;

/**
 * MAC token type implementation.
 *
 * TODO: This is not yet supported!
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenType extends TokenType
{
  public function getParent()
  {
    return 'token_type';
  }

  public function getName()
  {
    return 'mac';
  }
}
