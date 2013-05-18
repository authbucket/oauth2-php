<?php

/**
 * This file is part of the pantarei/oauth2 package.
 *
 * (c) Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pantarei\OAuth2\Extension\GrantType;

use Pantarei\OAuth2\Exception\InvalidGrantException;
use Pantarei\OAuth2\Exception\InvalidRequestException;
use Pantarei\OAuth2\Extension\GrantType;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Authorization code grant type implementation.
 *
 * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
 *
 * @author Wong Hoi Sing Edison <hswong3i@pantarei-design.com>
 */
class AuthorizationCodeGrantType extends GrantType
{
  /**
   * REQUIRED. Value MUST be set to "authorization_code".
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $grant_type = 'authorization_code';

  /**
   * REQUIRED. The authorization code received from the
   * authorization server.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $code = '';

  /**
   * REQUIRED, if the "redirect_uri" parameter was included in the
   * authorization request as described in Section 4.1.1, and their
   * values MUST be identical.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $redirect_uri;

  /**
   * REQUIRED, if the client is not authenticating with the
   * authorization server as described in Section 3.2.1.
   *
   * @see http://tools.ietf.org/html/rfc6749#section-4.1.3
   */
  private $cilentId;

  public function setCode($code)
  {
    $this->code = $code;
    return $this;
  }

  public function getCode()
  {
    return $this->code;
  }

  public function setRedirectUri($redirect_uri)
  {
    $this->redirect_uri = $redirect_uri;
    return $this;
  }

  public function getRedirectUri()
  {
    return $this->redirect_uri;
  }

  public function setClientId($client_id)
  {
    $this->client_id = $client_id;
    return $this;
  }

  public function getClientId()
  {
    return $this->client_id;
  }

  public function buildType()
  {
    $request = Request::createFromGlobals();

    // code is required.
    if (!$request->request->get('code')) {
      throw new InvalidRequestException();
    }

    // Validate code with database record.
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Codes')->findOneBy(array(
      'code' => $request->request->get('code'),
    ));
    if ($result === NULL) {
      throw new InvalidGrantException();
    }
    elseif ($result->getExpires() < time()) {
      throw new InvalidRequestException();
    }

    // redirect_uri is not required if already established via other channels,
    // check an existing redirect URI against the one supplied.
    $redirect_uri = NULL;
    $result = $this->app['oauth2.orm']->getRepository('Pantarei\OAuth2\Entity\Clients')->findOneBy(array(
      'client_id' => $request->request->get('client_id'),
    ));
    if ($result !== NULL && $result->getRedirectUri()) {
      $redirect_uri = $result->getRedirectUri();
    }

    // At least one of: existing redirect URI or input redirect URI must be
    // specified.
    if (!$redirect_uri && !$request->request->get('redirect_uri')) {
      throw new InvalidRequestException();
    }

    // If there's an existing uri and one from input, verify that they match.
    if ($redirect_uri && $request->request->get('redirect_uri')) {
      // Ensure that the input uri starts with the stored uri.
      if (strcasecmp(substr($request->request->get('redirect_uri'), 0, strlen($redirect_uri)), $redirect_uri) !== 0) {
        throw new InvalidRequestException();
      }
    }

    // code is required.
    $this->setCode($request->request->get('code'));

    // Set client_id from HTTP basic auth, or directly from POST.
    if ($request->getUser()) {
      $this->setClientId($request->getUser());
    }
    else {
      $this->setClientId($request->request->get('client_id'));
    }

    // Set redirect_uri from database record, or directly from GET.
    if ($redirect_uri) {
      $this->setRedirectUri($redirect_uri);
    }
    else {
      $this->setRedirectUri($request->request->get('redirect_uri'));
    }
  }

  public function getParent()
  {
    return 'grant_type';
  }

  public function getName()
  {
    return $this->grant_type;
  }
}
