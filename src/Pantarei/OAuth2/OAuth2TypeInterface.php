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
  public function __construct(Application $app);

  /**
   * Build the type.
   *
   * @param array $query
   *   The original query.
   * @param array $filtered_query
   *   The filtered query.
   */
  public function buildType($query, $filtered_query);

  /**
   *
   */
  public function buildView();

  /**
   *
   */
  public function finishView();

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
