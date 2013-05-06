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

use Pantarei\Oauth2\Exception\Exception;

/**
 * MAC token type implementation.
 *
 * TODO: This is not yet supported!
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class MacTokenType implements TokenTypeInterface
{
  public function getTokenType()
  {
    return 'mac';
  }

  public function __construct()
  {
    throw new Exception();
  }
}
