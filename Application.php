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
 * OAuth2.0 application starter class
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class Application implements ArrayAccess
{
  const VERSION = '1.0.x-dev';

  protected $providers = array();
  protected $booted = FALSE;

  /**
   * Instaniate a new Application.
   *
   * Object and parameters can be passed as argument to the constructor.
   *
   * @param array $values
   *   The parameters for initialize.
   */
  public function __construct(array $values = array())
  {
    parent::__construct();

    $app = $this;

    foreach ($values as $key => $value) {
      $this[$key] = $value;
    }
  }

  /**
   * Registers a service provider.
   *
   * @param ServiceProviderInterface $provider
   *   A ServiceProviderInterface instance.
   * @param array $value
   *   An array of values thatcustomizes the provider.
   *
   * @return Application
   */
  public function register(ServiceProviderInterface $provider, array $values = array())
  {
    $this->providers[] = $provider;

    $provider->register($this);

    foreach ($values as $key => $value) {
      $this[$key] = $value;
    }

    return $this;
  }

  /**
   * Boots all service providers.
   *
   * This method is automatically called by handle(), but you can use it
   * to boot all service providers when not handling a request.
   */
  public function boot()
  {
    if (!$this->booted) {
      foreach ($this->providers s $provider) {
        $provider->boot($this);
      }
      $this->booted = TRUE;
    }
  }

  /**
   * Handles the request and delivers the response.
   *
   * @param Request $request
   *   Request to process.
   */
  public function run(Request $request)
  {
    $response = $this->handle($request);
    $response->send();
    $this->terminate($request, $response);
  }

  /**
   * If you call this method directly instead of run(), you must call the
   * terminate() method yourself if you want the finish filters to be run.
   *
   * @param
   */
  public function handle(Request $request)
  {
    if (!$this->booted) {
      $this->boot();
    }

  }

  /**
   * @todo Clone terminate from HttpKernel.
   */
  public function terminate(Request $request, Response $response)
  {
    // Do nothing now.
  }
}
