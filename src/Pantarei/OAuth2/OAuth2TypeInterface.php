<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Base OAuth2 type interface for response, grant and token type.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface OAuth2TypeInterface
{
  /**
   * Constructor.
   *
   * @param Application $app
   *   An Application instance.
   */
  public function __construct(Request $request, Application $app);

  /**
   * Return a Response instance for feedback.
   *
   * @return Response
   *   An Response instance.
   */
  public function getResponse();

  /**
   * Returns the name of the parent type.
   *
   * @return string|null
   *   The name of the parent type if any, null otherwise.
   */
  public function getParent();

  /**
   * Returns the name of this type.
   *
   * @return string
   *   The name of this type.
   */
  public function getName();
}
