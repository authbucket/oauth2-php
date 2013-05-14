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

/**
 * Interface that must implement all OAuth2.0 service providers.
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
interface ServiceProviderInterface
{
  /**
   * Registers services on the given app.
   *
   * This method should only be used to configure services and parameters.
   * It should no get services.
   *
   * @param Application $app
   *   An Application instance.
   */
  public function register(Application $app);

  /**
   * Bootstraps the application.
   *
   * This method is called after all services are registers
   * and should be used for "dynamic" configuration (whenever
   * a service must be requested).
   *
   * @param Application $app
   *   An Application instance.
   */
  public function boot(Application $app);
}
