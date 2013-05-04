<?php

/*
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\Oauth2\TokenType;

/**
 * Bearer token type implementation.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class BearerTokenType implements TokenTypeInterface
{
  public function getTokenType()
  {
    return 'bearer';
  }
}
